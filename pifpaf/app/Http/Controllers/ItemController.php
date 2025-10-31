<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
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
     * Analyse une image avec l'IA (simulation) et redirige vers le formulaire de création.
     */
    public function analyzeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Simuler l'analyse de l'IA
        $aiData = [
            'title' => 'Objet Détecté par l\'IA',
            'description' => 'Ceci est une description générée automatiquement par notre IA.',
            'category' => 'Électronique', // Catégorie suggérée
            'price' => 99.99, // Prix suggéré
        ];

        // Stocker l'image temporairement
        $imagePath = $request->file('image')->store('temp_images', 'public');

        // Rediriger vers le formulaire de création avec les données pré-remplies
        return redirect()->route('items.create')->with([
            'ai_data' => $aiData,
            'image_path' => $imagePath
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
            'image' => 'required_without:image_path|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_path' => 'sometimes|string',
            'pickup_available' => 'sometimes|boolean',
        ]);

        $imagePath = $request->input('image_path');

        // Si une nouvelle image est uploadée, elle a la priorité
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image temporaire si elle existe
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('images', 'public');
        } elseif ($imagePath) {
            // Si aucune nouvelle image n'est uploadée mais qu'un chemin temporaire existe,
            // on déplace l'image vers le dossier final.
            $newPath = 'images/' . basename($imagePath);
            Storage::disk('public')->move($imagePath, $newPath);
            $imagePath = $newPath;
        }

        $item = new Item($validatedData);
        $item->pickup_available = $request->has('pickup_available');
        $item->image_path = $imagePath;
        $item->user_id = Auth::id();
        $item->save();

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
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pickup_available' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            Storage::disk('public')->delete($item->image_path);

            // Enregistrer la nouvelle image
            $imagePath = $request->file('image')->store('images', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $validatedData['pickup_available'] = $request->has('pickup_available');

        $item->update($validatedData);

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
     * Supprime une annonce de la base de données.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        // Supprimer l'image associée
        Storage::disk('public')->delete($item->image_path);

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
