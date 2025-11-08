<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Transaction;
use App\Models\WalletHistory;
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
        $initialWalletBalance = $user->wallet;
        $offerAmount = $offer->amount;
        $useWallet = $request->boolean('use_wallet');

        $walletAmountToUseFromInitialBalance = 0;
        $cardAmount = $offerAmount;

        // Calculer le montant à payer par carte en fonction de l'utilisation du portefeuille
        if ($useWallet && $initialWalletBalance > 0) {
            $walletAmountToUseFromInitialBalance = min($initialWalletBalance, $offerAmount);
            $cardAmount = $offerAmount - $walletAmountToUseFromInitialBalance;
        }

        $cardPaymentSuccessful = false;
        // Validation du paiement Stripe si un paiement par carte est nécessaire
        if ($cardAmount > 0) {
            $paymentIntentId = $request->input('payment_intent_id');

            if (!$paymentIntentId) {
                return back()->withErrors(['payment' => 'Le paiement n\'a pas pu être traité. Veuillez réessayer.']);
            }

            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            try {
                $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

                if ($intent->status !== 'succeeded') {
                    return back()->withErrors(['payment' => 'Le paiement a échoué. Veuillez vérifier les informations de votre carte.']);
                }

                // Vérification de sécurité : le montant payé correspond-il au montant attendu ?
                $expectedAmountInCents = (int) round($cardAmount * 100);
                if ($intent->amount !== $expectedAmountInCents) {
                    return back()->withErrors(['payment' => 'Une erreur de montant est survenue. Le paiement a été annulé.']);
                }
                $cardPaymentSuccessful = true;
            } catch (\Exception $e) {
                return back()->withErrors(['payment' => 'Erreur lors de la vérification du paiement: ' . $e->getMessage()]);
            }
        }

        // Vérifier si le solde total (portefeuille + carte) est suffisant
        $totalAvailableAmount = $initialWalletBalance + ($cardPaymentSuccessful ? $cardAmount : 0);
        if ($totalAvailableAmount < $offerAmount) {
            return back()->withErrors(['payment' => 'Solde insuffisant pour finaliser la transaction.']);
        }

        // Début de la transaction de base de données pour garantir l'intégrité
        $transaction = DB::transaction(function () use ($user, $offer, $cardAmount, $offerAmount, $cardPaymentSuccessful) {
            // 1. Créditer le portefeuille si un paiement par carte a été effectué
            if ($cardPaymentSuccessful && $cardAmount > 0) {
                $user->wallet += $cardAmount;
                // La sauvegarde finale se fera à la fin de la transaction
            }

            // 2. Créer la transaction Pifpaf
            $newTransaction = Transaction::create([
                'offer_id' => $offer->id,
                'amount' => $offerAmount,
                'wallet_amount' => $offerAmount, // Le montant total est payé via le portefeuille
                'card_amount' => 0,             // Le paiement par carte est maintenant une transaction de portefeuille
                'status' => 'payment_received',
                'pickup_code' => $offer->item->pickup_available ? Str::random(6) : null,
            ]);

            // 3. Créer les entrées dans l'historique du portefeuille
            if ($cardPaymentSuccessful && $cardAmount > 0) {
                WalletHistory::create([
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $cardAmount,
                    'description' => 'Crédit via carte pour l\'achat : ' . $offer->item->title,
                    'transaction_id' => $newTransaction->id,
                ]);
            }

            // Créer l'entrée de débit pour le montant total de l'offre
            WalletHistory::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $offerAmount,
                'description' => 'Achat de l\'article : ' . $offer->item->title,
                'transaction_id' => $newTransaction->id,
            ]);


            // 4. Débiter le montant total de l'offre du portefeuille
            $user->wallet -= $offerAmount;
            $user->save();

            // 5. Mise à jour des statuts
            $offer->update(['status' => 'paid']);
            $offer->item->update(['status' => 'sold']);

            return $newTransaction;
        });

        return redirect()->route('checkout.success', ['transaction' => $transaction]);
    }
}
