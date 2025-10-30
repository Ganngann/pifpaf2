<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        // Préparer les données de la transaction
        $transactionData = [
            'offer_id' => $offer->id,
            'amount' => $offer->amount,
            'status' => 'payment_received', // Le paiement est séquestré jusqu'à confirmation
        ];

        // Si l'article est en retrait sur place, générer un code
        if ($offer->item->pickup_available) {
            $transactionData['pickup_code'] = Str::random(6);
        }

        // Création de la transaction
        Transaction::create($transactionData);

        // Mise à jour du statut de l'offre
        $offer->update(['status' => 'paid']);

        // Mise à jour du statut de l'article
        $item = $offer->item;
        $item->update(['status' => 'sold']);

        // Le vendeur n'est pas crédité ici. Le paiement est déclenché par la confirmation de l'acheteur.

        return redirect()->route('dashboard')->with('success', 'Paiement effectué avec succès ! Votre commande est en attente de confirmation de réception.');
    }
}
