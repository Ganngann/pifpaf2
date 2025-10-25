<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    use AuthorizesRequests;
    /**
     * Affiche le tableau de bord avec les annonces de l'utilisateur.
     */
    public function index()
    {
        $items = Auth::user()->items()->latest()->get();

        return view('dashboard', [
            'items' => $items,
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
            'price' => 'required|numeric',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            Storage::disk('public')->delete($item->image_path);

            // Enregistrer la nouvelle image
            $imagePath = $request->file('image')->store('images', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $item->update($validatedData);

        return redirect()->route('dashboard')->with('success', 'Annonce mise à jour avec succès.');
    }
}
