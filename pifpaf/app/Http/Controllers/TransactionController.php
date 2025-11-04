<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WalletHistory;
use App\Services\SendcloudService;
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

        // Enregistrer l'historique du portefeuille pour le vendeur (crédit)
        WalletHistory::create([
            'user_id' => $seller->id,
            'type' => 'credit',
            'amount' => $transaction->amount,
            'description' => 'Vente de l\'article : ' . $transaction->offer->item->title,
        ]);

        // Enregistrer l'historique du portefeuille pour l'acheteur (débit)
        $buyer = $transaction->offer->user;
        WalletHistory::create([
            'user_id' => $buyer->id,
            'type' => 'debit',
            'amount' => $transaction->amount,
            'description' => 'Achat de l\'article : ' . $transaction->offer->item->title,
        ]);

        return redirect()->route('dashboard')->with('success', 'Réception confirmée. Le vendeur a été payé.');
    }

    /**
     * Affiche l'historique des achats de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function purchases()
    {
        $purchases = Transaction::whereHas('offer', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('offer.item.primaryImage', 'offer.item.user')->latest()->paginate(10);

        return view('transactions.purchases', compact('purchases'));
    }

    /**
     * Affiche l'historique des ventes de l'utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function sales()
    {
        $sales = Transaction::whereHas('offer.item', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('offer.item.primaryImage', 'offer.user')->latest()->paginate(10);

        return view('transactions.sales', compact('sales'));
    }

    /**
     * Create a shipment for a transaction.
     *
     * @param \App\Models\Transaction $transaction
     * @param \App\Services\SendcloudService $sendcloudService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createShipment(Transaction $transaction, SendcloudService $sendcloudService)
    {
        $this->authorize('update', $transaction->offer->item);

        // For now, we'll use a hardcoded shipping method ID.
        // In a real application, this would come from the user's choice.
        $shippingMethodId = 8; // Unstamped letter (for testing)

        $response = $sendcloudService->createParcel(
            $transaction->offer->item,
            $transaction->shippingAddress,
            $shippingMethodId
        );

        if ($response->successful()) {
            $parcelData = $response->json('parcel');
            $transaction->update([
                'sendcloud_parcel_id' => data_get($parcelData, 'id'),
                'tracking_code' => data_get($parcelData, 'tracking_number'),
                'label_url' => data_get($parcelData, 'label.label_printer'),
                'status' => 'shipping_initiated',
            ]);

            return redirect()->route('dashboard')->with('success', 'Envoi créé avec succès.');
        }

        return redirect()->route('dashboard')->with('error', 'Erreur lors de la création de l\'envoi.');
    }
}
