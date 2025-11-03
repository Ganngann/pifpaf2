<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Models\AiRequest;
use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Services\GoogleAiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Affiche la page d'accueil avec les derniers articles.
     */
    public function welcome(Request $request)
    {
        $query = Item::query()->with('primaryImage')->available()->latest();

        // Recherche par mot-clé dans le titre ou la description
        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = '%' . $request->input('search') . '%';
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', $searchTerm)
                         ->orWhere('description', 'like', $searchTerm);
            });
        });

        // Filtre par catégorie
        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->where('category', $request->input('category'));
        });

        // Filtre par prix minimum
        $query->when($request->filled('min_price'), function ($q) use ($request) {
            $q->where('price', '>=', $request->input('min_price'));
        });

        // Filtre par prix maximum
        $query->when($request->filled('max_price'), function ($q) use ($request) {
            $q->where('price', '<=', $request->input('max_price'));
        });

        // Filtre par distance
        if ($request->filled('location') && $request->filled('distance')) {
            $locationString = $request->input('location');
            $distanceInKm = (int) $request->input('distance');

            // 1. Géocoder l'adresse de recherche
            $response = Http::get('https://geocode.maps.co/search', ['q' => $locationString]);

            if ($response->successful() && count($response->json()) > 0) {
                $geocodedLocation = $response->json()[0];
                $latitude = $geocodedLocation['lat'];
                $longitude = $geocodedLocation['lon'];

                // 2. Filtrer les items en utilisant la formule Haversine (compatible SQLite)
                $radiusInKm = $distanceInKm;

                // Formule de Haversine pour calculer la distance
                // 6371 est le rayon de la Terre en kilomètres.
                $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

                $addressIds = DB::table('pickup_addresses')
                    ->select('id')
                    ->whereRaw("{$haversine} < ?", [$latitude, $longitude, $latitude, $radiusInKm])
                    ->pluck('id');

                $query->whereIn('pickup_address_id', $addressIds)
                      ->where('pickup_available', true);
            }
        }

        $items = $query->get();

        return view('welcome', [
            'items' => $items,
        ]);
    }

    /**
     * Affiche le formulaire de création d'annonce via l'IA.
     */
    public function createWithAi()
    {
        return view('items.create-with-ai');
    }

    /**
     * Affiche le tableau de bord avec les annonces de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        // Pour le vendeur, on charge les articles avec leur image principale et les offres
        $items = $user->items()
            ->with('primaryImage', 'offers.transaction', 'offers.user')
            ->latest()
            ->get();
        // Pour l'acheteur, on charge les offres avec la transaction
        $offers = $user->offers()->with('item.user', 'transaction')->latest()->get();

        $completedSales = $items->filter(function ($item) {
            return $item->status === \App\Enums\ItemStatus::SOLD && $item->offers->where('status', 'paid')->contains(function ($offer) {
                return $offer->transaction && $offer->transaction->status === 'completed';
            });
        })->take(5);

        return view('dashboard', [
            'items' => $items,
            'offers' => $offers,
            'completedSales' => $completedSales,
        ]);
    }

    /**
     * Affiche le formulaire de création d'annonce.
     */
    public function create()
    {
        $pickupAddresses = Auth::user()->pickupAddresses;
        return view('items.create', compact('pickupAddresses'));
    }


    /**
     * Crée une annonce non publiée à partir d'une sélection IA (AJAX).
     */
    public function createFromAi(Request $request)
    {
        $validated = $request->validate([
            'original_image_path' => 'required|string',
            'item_data' => 'required|string',
            'item_index' => 'required|integer',
        ]);

        $itemData = json_decode($validated['item_data'], true);
        $originalPath = $validated['original_image_path'];
        $itemIndex = $validated['item_index'];
        $box = $itemData['box'];

        $aiRequest = AiRequest::where('image_path', $originalPath)->first();

        if (!$aiRequest) {
            return response()->json(['success' => false, 'message' => 'Requête IA non trouvée.']);
        }

        if ($aiRequest->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'L\'analyse IA n\'est pas terminée.']);
        }

        $createdItemIds = $aiRequest->created_item_ids ?? [];
        if (isset($createdItemIds[$itemIndex])) {
            return response()->json(['success' => false, 'message' => 'Cet objet a déjà été créé.']);
        }


        if (!Storage::disk('public')->exists($originalPath)) {
            return response()->json(['success' => false, 'message' => 'Image originale non trouvée.']);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->read(Storage::disk('public')->path($originalPath));

        $x1 = $box['x1'] / 1000.0;
        $y1 = $box['y1'] / 1000.0;
        $x2 = $box['x2'] / 1000.0;
        $y2 = $box['y2'] / 1000.0;

        $width = ($x2 - $x1) * $image->width();
        $height = ($y2 - $y1) * $image->height();
        $x = $x1 * $image->width();
        $y = $y1 * $image->height();

        $croppedImage = $image->crop((int)$width, (int)$height, (int)$x, (int)$y);
        $croppedImageName = 'cropped_' . uniqid() . '.jpg';

        $item = Item::create([
            'user_id' => Auth::id(),
            'title' => $itemData['title'],
            'description' => $itemData['description'],
            'price' => $itemData['price'],
            'category' => $itemData['category'],
            'status' => ItemStatus::UNPUBLISHED,
        ]);

        $croppedImagePath = "item_images/{$item->id}/" . $croppedImageName;
        Storage::disk('public')->put($croppedImagePath, (string) $croppedImage->encode());

        $item->images()->create([
            'path' => $croppedImagePath,
            'is_primary' => true,
            'order' => 0,
        ]);

        $createdItemIds[$itemIndex] = $item->id;
        $aiRequest->update(['created_item_ids' => $createdItemIds]);


        return response()->json(['success' => true, 'item_url' => route('items.show', $item)]);
    }


    /**
     * Enregistre une nouvelle annonce dans la base de données.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category' => 'required|string|in:Vêtements,Électronique,Maison,Sport,Loisirs,Autre',
            'price' => 'required|numeric',
            'images' => 'required_without:image_path|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'image_path' => 'sometimes|string',
            'delivery_available' => 'sometimes|boolean',
            'pickup_available' => 'sometimes|boolean',
            'pickup_address_id' => 'required_if:pickup_available,true|nullable|exists:pickup_addresses,id',
        ]);

        $item = new Item($validatedData);
        $item->delivery_available = $request->boolean('delivery_available');
        $item->pickup_available = $request->boolean('pickup_available');
        // N'assigner pickup_address_id que si le retrait est activé
        $item->pickup_address_id = $request->boolean('pickup_available') ? $validatedData['pickup_address_id'] : null;

        $item->user_id = Auth::id();
        $item->save();

        // Gestion des images
        $order = 0;
        // 1. Gérer l'image venant du flux IA
        if ($request->has('image_path')) {
            $tempPath = $request->input('image_path');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = "item_images/{$item->id}/" . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);

                $item->images()->create([
                    'path' => $newPath,
                    'is_primary' => true,
                    'order' => $order++,
                ]);
            }
        }

        // 2. Gérer les images uploadées depuis le formulaire
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store("item_images/{$item->id}", 'public');
                $isPrimary = ($order === 0); // La toute première image est la principale

                $item->images()->create([
                    'path' => $path,
                    'is_primary' => $isPrimary,
                    'order' => $order++,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Annonce créée avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'une annonce.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);
        $pickupAddresses = Auth::user()->pickupAddresses;

        return view('items.edit', [
            'item' => $item,
            'pickupAddresses' => $pickupAddresses,
        ]);
    }

    /**
     * Met à jour une annonce dans la base de données.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category' => 'required|string|in:Vêtements,Électronique,Maison,Sport,Loisirs,Autre',
            'price' => 'required|numeric',
            'images' => 'sometimes|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'delivery_available' => 'sometimes|boolean',
            'pickup_available' => 'sometimes|boolean',
            'pickup_address_id' => 'required_if:pickup_available,true|nullable|exists:pickup_addresses,id',
        ]);

        $validatedData['delivery_available'] = $request->boolean('delivery_available');
        $validatedData['pickup_available'] = $request->boolean('pickup_available');
        // N'assigner pickup_address_id que si le retrait est activé
        $validatedData['pickup_address_id'] = $request->boolean('pickup_available') ? $validatedData['pickup_address_id'] : null;

        $item->update($validatedData);

        // Ajout de nouvelles images
        if ($request->hasFile('images')) {
            // On récupère le dernier ordre pour continuer la séquence
            $order = $item->images()->max('order') + 1;

            foreach ($request->file('images') as $imageFile) {
                // On vérifie qu'on ne dépasse pas la limite totale de 10 images
                if ($item->images()->count() >= 10) {
                    break;
                }

                $path = $imageFile->store("item_images/{$item->id}", 'public');
                $item->images()->create([
                    'path' => $path,
                    'order' => $order++,
                ]);
            }
        }


        return redirect()->route('dashboard')->with('success', 'Annonce mise à jour avec succès.');
    }

    /**
     * Dépublie une annonce.
     */
    public function unpublish(Item $item)
    {
        $this->authorize('update', $item);

        $item->status = ItemStatus::UNPUBLISHED;
        $item->save();

        return redirect()->route('dashboard')->with('success', 'Annonce dépubliée avec succès.');
    }

    /**
     * Republie une annonce.
     */
    public function publish(Item $item)
    {
        $this->authorize('update', $item);

        $item->status = ItemStatus::AVAILABLE;
        $item->save();

        return redirect()->route('dashboard')->with('success', 'Annonce publiée avec succès.');
    }

    /**
     * Supprime une annonce de la base de données.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        // Supprimer le dossier contenant les images de l'annonce
        Storage::disk('public')->deleteDirectory("item_images/{$item->id}");

        $item->delete();

        return redirect()->route('dashboard')->with('success', 'Annonce supprimée avec succès.');
    }

    /**
     * Affiche la page de détail d'une annonce.
     */
    public function show(Item $item)
    {
        return view('items.show', [
            'item' => $item,
        ]);
    }

    public function toggleStatus(Item $item)
    {
        $this->authorize('update', $item);

        if ($item->status === \App\Enums\ItemStatus::AVAILABLE) {
            $item->status = \App\Enums\ItemStatus::UNPUBLISHED;
        } else {
            $item->status = \App\Enums\ItemStatus::AVAILABLE;
        }

        $item->save();

        return response()->json([
            'newStatus' => $item->status,
            'newStatusText' => $item->status === \App\Enums\ItemStatus::AVAILABLE ? 'En ligne' : 'Hors ligne',
            'isAvailable' => $item->status === \App\Enums\ItemStatus::AVAILABLE,
        ]);
    }
}
