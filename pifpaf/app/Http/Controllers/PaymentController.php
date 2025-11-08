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

        $user = Auth::user();
        $walletBalance = $user->wallet ?? 0;

        return view('payment.create', [
            'offer' => $offer,
            'walletBalance' => $walletBalance,
        ]);
    }

    /**
     * Traite le paiement en utilisant le portefeuille comme intermédiaire.
     */
    public function store(Request $request, Offer $offer)
    {
        // Validation des données de base
        $request->validate([
            'use_wallet' => 'required|boolean',
            'wallet_amount' => 'required|numeric|min:0',
            'card_amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // On s'assure que l'utilisateur connecté est bien l'acheteur
        if ($user->id !== $offer->user_id) {
            abort(403, 'Accès non autorisé.');
        }

        // On vérifie que l'offre a bien été acceptée
        if ($offer->status !== 'accepted') {
            return back()->withErrors(['payment' => 'Cette offre n\'est pas prête pour le paiement.']);
        }

        $totalAmount = $offer->amount;
        $useWallet = $request->boolean('use_wallet');
        $cardAmount = (float) $request->input('card_amount', 0);
        $walletAmountToUse = (float) $request->input('wallet_amount', 0);

        // On vérifie la cohérence des montants
        if (abs(($walletAmountToUse + $cardAmount) - $totalAmount) > 0.01) {
            return back()->withErrors(['payment' => 'Les montants de paiement sont incohérents.']);
        }

        // Si l'utilisateur paie par carte, on crédite d'abord son portefeuille (simulation)
        if ($cardAmount > 0) {
            $user->wallet += $cardAmount;
            // Dans une application réelle, on ajouterait ici une transaction de "crédit"
            // dans l'historique du portefeuille.
        }

        // On vérifie si le solde du portefeuille (après crédit éventuel) est suffisant
        if ($user->wallet < $totalAmount) {
             // Si on a crédité le portefeuille, il faut annuler l'opération
            if ($cardAmount > 0) {
                $user->wallet -= $cardAmount;
            }
            return back()->withErrors(['payment' => 'Votre solde est insuffisant pour compléter la transaction.']);
        }

        // On débite le portefeuille du montant total de l'offre
        $user->wallet -= $totalAmount;
        $user->save();

        // Préparer les données de la transaction
        $transactionData = [
            'offer_id' => $offer->id,
            'amount' => $totalAmount,
            'status' => 'payment_received', // Le paiement est séquestré jusqu'à confirmation
            'wallet_amount' => $walletAmountToUse,
            'card_amount' => $cardAmount,
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

        return redirect()->route('dashboard')->with('success', 'Paiement effectué avec succès ! Votre commande est en attente de confirmation de réception.');
    }
}
