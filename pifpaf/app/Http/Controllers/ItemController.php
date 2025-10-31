<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Services\GoogleAiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    use AuthorizesRequests;

    /**
     * Affiche la page d'accueil avec les derniers articles.
     */
    public function welcome(Request $request)
    {
        $query = Item::query()->available()->latest();

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
        // Pour le vendeur, on charge les offres avec leurs transactions
        $items = $user->items()->with('offers.transaction', 'offers.user')->latest()->get();
        // Pour l'acheteur, on charge les offres avec la transaction
        $offers = $user->offers()->with('item.user', 'transaction')->latest()->get();

        return view('dashboard', [
            'items' => $items,
            'offers' => $offers,
        ]);
    }

    /**
     * Affiche le formulaire de création d'annonce.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Analyse une image avec l'IA et redirige vers le formulaire de création.
     */
    public function analyzeImage(Request $request, GoogleAiService $aiService)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageFile = $request->file('image');
        $imagePath = $imageFile->getRealPath();

        $aiData = $aiService->analyzeImage($imagePath);

        if (!$aiData) {
            return back()->with('error', 'L\'analyse de l\'image a échoué. Veuillez réessayer.');
        }

        // Stocker l'image temporairement
        $storedImagePath = $imageFile->store('temp_images', 'public');

        // Rediriger vers le formulaire de création avec les données pré-remplies
        return redirect()->route('items.create')->with([
            'ai_data' => $aiData,
            'image_path' => $storedImagePath
        ]);
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
            'pickup_available' => 'sometimes|boolean',
        ]);

        $item = new Item($validatedData);
        $item->pickup_available = $request->has('pickup_available');
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

        return view('items.edit', [
            'item' => $item,
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
            'pickup_available' => 'sometimes|boolean',
        ]);

        $validatedData['pickup_available'] = $request->has('pickup_available');
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
}
