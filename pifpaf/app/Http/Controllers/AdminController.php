<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Dispute;
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

    /**
     * Affiche la liste des litiges.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function disputesIndex(Request $request)
    {
        $query = Dispute::with(['transaction.offer.item', 'user'])->where('status', 'open');

        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('reason', 'like', $searchTerm)
                  ->orWhereHas('user', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', $searchTerm);
                  });
        }

        $disputes = $query->latest()->paginate(15);

        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Affiche les détails d'un litige.
     *
     * @param  \App\Models\Dispute  $dispute
     * @return \Illuminate\View\View
     */
    public function disputesShow(Dispute $dispute)
    {
        $dispute->load(['transaction.offer.item.user', 'transaction.offer.user', 'user']);

        $transaction = $dispute->transaction;
        $item = $transaction->offer->item;
        $buyer = $transaction->offer->user;
        $seller = $item->user;

        // Trouver la conversation liée à cet item entre l'acheteur et le vendeur
        $conversation = Conversation::where('item_id', $item->id)
            ->where('buyer_id', $buyer->id)
            ->where('seller_id', $seller->id)
            ->with('messages.user')
            ->first();

        return view('admin.disputes.show', compact('dispute', 'transaction', 'item', 'buyer', 'seller', 'conversation'));
    }

    /**
     * Résout un litige en faveur de l'acheteur (remboursement).
     *
     * @param  \App\Models\Dispute  $dispute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolveForBuyer(Dispute $dispute)
    {
        $transaction = $dispute->transaction;
        $buyer = $transaction->offer->user;

        // Recréditer le portefeuille de l'acheteur
        $buyer->wallet += $transaction->amount;
        $buyer->save();

        // Mettre à jour les statuts
        $dispute->update(['status' => 'closed']);
        $transaction->update(['status' => 'refunded']);

        return redirect()->route('admin.disputes.index')->with('success', 'Litige résolu en faveur de l\'acheteur. Le montant a été remboursé.');
    }

    /**
     * Résout un litige en faveur du vendeur.
     *
     * @param  \App\Models\Dispute  $dispute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolveForSeller(Dispute $dispute)
    {
        $transaction = $dispute->transaction;
        $seller = $transaction->offer->item->user;

        // Créditer le portefeuille du vendeur
        $seller->wallet += $transaction->amount;
        $seller->save();

        // Mettre à jour les statuts
        $dispute->update(['status' => 'closed']);
        $transaction->update(['status' => 'completed']);

        return redirect()->route('admin.disputes.index')->with('success', 'Litige résolu en faveur du vendeur. Le montant a été transféré.');
    }

    /**
     * Clôture un litige sans action financière.
     *
     * @param  \App\Models\Dispute  $dispute
     * @return \Illuminate\Http\RedirectResponse
     */
    public function closeDispute(Dispute $dispute)
    {
        $dispute->update(['status' => 'closed']);
        return redirect()->route('admin.disputes.index')->with('success', 'Le litige a été clôturé.');
    }
}
