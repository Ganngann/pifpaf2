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
}
