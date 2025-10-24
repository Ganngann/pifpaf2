<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
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
}
