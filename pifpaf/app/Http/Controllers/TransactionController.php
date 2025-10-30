<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Confirme le retrait d'un article.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmPickup(Transaction $transaction)
    {
        // On s'assure que l'utilisateur connecté est bien le vendeur de l'article concerné
        $this->authorize('update', $transaction->offer->item);

        // Mettre à jour le statut de la transaction
        $transaction->update(['status' => 'pickup_completed']);

        return redirect()->route('dashboard')->with('success', 'Retrait confirmé avec succès.');
    }

    /**
     * Confirme la réception d'un article par l'acheteur.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmReception(Transaction $transaction)
    {
        // On s'assure que l'utilisateur connecté est bien l'acheteur
        if (Auth::id() !== $transaction->offer->user_id) {
            abort(403);
        }

        // On vérifie que la transaction est bien en attente de confirmation
        if ($transaction->status !== 'payment_received') {
            return redirect()->route('dashboard')->with('error', 'Cette transaction n\'est pas en attente de confirmation.');
        }

        // Mettre à jour le statut de la transaction
        $transaction->update(['status' => 'completed']);

        // Créditer le portefeuille du vendeur
        $seller = $transaction->offer->item->user;
        $seller->wallet += $transaction->amount;
        $seller->save();

        return redirect()->route('dashboard')->with('success', 'Réception confirmée. Le vendeur a été payé.');
    }
}
