<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        $walletBalance = Auth::user()->wallet;

        // Le montant à payer par carte est le montant de l'offre, potentiellement réduit du solde du portefeuille.
        // Par défaut, nous créons l'intention pour le montant total. Le front-end mettra à jour ce montant si nécessaire.
        $amountToPayByCard = $offer->amount;

        // Stripe attend un montant en centimes.
        $amountInCents = round($amountToPayByCard * 100);

        if ($amountInCents < 50) { // Stripe a un montant minimum (généralement 0.50 EUR)
             // Si le montant est trop faible pour être payé par carte (parce que le portefeuille couvre presque tout),
            // on ne crée pas d'intention de paiement. Le paiement se fera uniquement via le portefeuille.
            $intent = null;
        } else {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'eur',
                'automatic_payment_methods' => ['enabled' => true],
            ]);
        }


        return view('payment.create', [
            'offer' => $offer,
            'walletBalance' => $walletBalance,
            'intent' => $intent,
        ]);
    }

    /**
     * Traite le paiement simulé.
     */
    public function store(Request $request, Offer $offer)
    {
        // Validation et vérifications initiales
        if (Auth::id() !== $offer->user_id) {
            abort(403, 'Accès non autorisé.');
        }
        if ($offer->status !== 'accepted') {
            return back()->withErrors(['payment' => 'Cette offre n\'est plus disponible pour le paiement.']);
        }

        $user = Auth::user();
        $walletBalance = $user->wallet;
        $offerAmount = $offer->amount;
        $useWallet = $request->boolean('use_wallet');

        $walletAmountToUse = 0;
        $cardAmount = $offerAmount;

        if ($useWallet && $walletBalance > 0) {
            $walletAmountToUse = min($walletBalance, $offerAmount);
            $cardAmount = $offerAmount - $walletAmountToUse;
        }

        // Validation du paiement Stripe si un paiement par carte est nécessaire
        if ($cardAmount > 0) {
            $paymentIntentId = $request->input('payment_intent_id');

            if (!$paymentIntentId) {
                return back()->withErrors(['payment' => 'Le paiement n\'a pas pu être traité. Veuillez réessayer.']);
            }

            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            if ($intent->status !== 'succeeded') {
                return back()->withErrors(['payment' => 'Le paiement a échoué. Veuillez vérifier les informations de votre carte.']);
            }

            // Vérification de sécurité : le montant payé correspond-il au montant attendu ?
            $expectedAmountInCents = (int) round($cardAmount * 100);
            if ($intent->amount !== $expectedAmountInCents) {
                // Potentielle fraude ou erreur, on ne finalise pas
                // On pourrait aussi rembourser le paiement ici
                return back()->withErrors(['payment' => 'Une erreur de montant est survenue. Le paiement a été annulé.']);
            }
        }

        // Début de la transaction de base de données pour garantir l'intégrité
        DB::transaction(function () use ($user, $offer, $walletAmountToUse, $cardAmount, $offerAmount) {
            // Mettre à jour le solde du portefeuille si utilisé
            if ($walletAmountToUse > 0) {
                $user->wallet -= $walletAmountToUse;
                $user->save();
            }

            // Préparer les données de la transaction
            $transactionData = [
                'offer_id' => $offer->id,
                'amount' => $offerAmount,
                'wallet_amount' => $walletAmountToUse,
                'card_amount' => $cardAmount,
                'status' => 'payment_received',
            ];

            if ($offer->item->pickup_available) {
                $transactionData['pickup_code'] = Str::random(6);
            }

            // Création de la transaction
            Transaction::create($transactionData);

            // Mise à jour des statuts
            $offer->update(['status' => 'paid']);
            $offer->item->update(['status' => 'sold']);
        });

        return redirect()->route('dashboard')->with('success', 'Paiement effectué avec succès !');
    }
}
