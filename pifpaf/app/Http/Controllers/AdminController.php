<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

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
}
