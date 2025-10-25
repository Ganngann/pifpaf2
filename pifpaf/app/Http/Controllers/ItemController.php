<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Affiche le tableau de bord du vendeur avec ses annonces.
     */
    public function index()
    {
        $items = Auth::user()->items;
        return view('dashboard', compact('items'));
    }

    /**
     * Affiche le formulaire de création d'annonce.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Enregistre une nouvelle annonce dans la base de données.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('images', 'public');

        $item = new Item($validatedData);
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
        Gate::authorize('update', $item);
        return view('items.edit', compact('item'));
    }

    /**
     * Met à jour une annonce dans la base de données.
     */
    public function update(Request $request, Item $item)
    {
        Gate::authorize('update', $item);

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            Storage::disk('public')->delete($item->image_path);
            $validatedData['image_path'] = $request->file('image')->store('images', 'public');
        }

        $item->update($validatedData);

        return redirect()->route('dashboard')->with('success', 'Annonce modifiée avec succès.');
    }

    /**
     * Supprime une annonce de la base de données.
     */
    public function destroy(Item $item)
    {
        Gate::authorize('delete', $item);

        // Supprimer l'image associée
        Storage::disk('public')->delete($item->image_path);

        $item->delete();

        return redirect()->route('dashboard')->with('success', 'Annonce supprimée avec succès.');
    }
}
