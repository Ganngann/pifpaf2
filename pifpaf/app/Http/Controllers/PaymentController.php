<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Affiche le formulaire de paiement.
     */
    public function create(Offer $offer)
    {
        // On s'assure que l'utilisateur connecté est bien l'acheteur
        if (Auth::id() !== $offer->user_id) {
            abort(403, 'Accès non autorisé.');
        }

        // On vérifie que l'offre a bien été acceptée
        if ($offer->status !== 'accepted') {
            return redirect()->route('dashboard')->withErrors(['payment' => 'Cette offre n\'est pas prête pour le paiement.']);
        }

        return view('payment.create', [
            'offer' => $offer,
        ]);
    }

    /**
     * Traite le paiement simulé.
     */
    public function store(Request $request, Offer $offer)
    {
        // On s'assure que l'utilisateur connecté est bien l'acheteur
        if (Auth::id() !== $offer->user_id) {
            abort(403, 'Accès non autorisé.');
        }

        // On vérifie que l'offre a bien été acceptée
        if ($offer->status !== 'accepted') {
            return redirect()->route('dashboard')->withErrors(['payment' => 'Cette offre n\'est pas prête pour le paiement.']);
        }

        // Création de la transaction
        Transaction::create([
            'offer_id' => $offer->id,
            'amount' => $offer->amount,
            'status' => 'completed',
        ]);

        // Mise à jour du statut de l'offre
        $offer->update(['status' => 'paid']);

        // Mise à jour du statut de l'article
        $offer->item->update(['status' => 'sold']);

        return redirect()->route('dashboard')->with('success', 'Paiement effectué avec succès !');
    }
}
