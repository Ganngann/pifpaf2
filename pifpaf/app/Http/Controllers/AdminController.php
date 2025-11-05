<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administration.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userCount = User::count();
        $itemCount = Item::count();
        $transactionCount = Transaction::count();

        return view('admin.dashboard', compact('userCount', 'itemCount', 'transactionCount'));
    }

    /**
     * Affiche la liste des utilisateurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function usersIndex(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Bannit un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ban(User $user)
    {
        $user->update(['banned_at' => now()]);
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur banni avec succès.');
    }

    /**
     * Réactive un utilisateur banni.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur réactivé avec succès.');
    }

    /**
     * Affiche la liste des annonces.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function itemsIndex(Request $request)
    {
        $query = Item::with('user');

        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhereHas('user', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', $searchTerm);
                  });
        }

        $items = $query->paginate(15);

        return view('admin.items.index', compact('items'));
    }

    /**
     * Supprime une annonce.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyItem(Item $item)
    {
        // Supprimer les images associées de l'espace de stockage
        foreach ($item->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Annonce supprimée avec succès.');
    }
}
