<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WalletHistory;
use App\Services\SendcloudService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Enums\TransactionStatus;
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
        $transaction->update(['status' => TransactionStatus::PICKUP_COMPLETED]);

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
        if ($transaction->status !== TransactionStatus::PAYMENT_RECEIVED) {
            return redirect()->route('dashboard')->with('error', 'Cette transaction n\'est pas en attente de confirmation.');
        }

        // Mettre à jour le statut de la transaction
        $transaction->update(['status' => TransactionStatus::COMPLETED]);

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
        })->with(['offer.item.primaryImage', 'offer.item.user', 'reviews'])->latest()->paginate(10);

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
     * Affiche les détails d'une transaction.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\View\View
     */
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);

        $transaction->load('offer.item.user', 'offer.user', 'reviews', 'shippingAddress');

        return view('transactions.show', compact('transaction'));
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

        if (!$transaction->shippingAddress) {
            return redirect()->back()->with('error', 'Cette transaction ne nécessite pas d\'expédition car elle n\'a pas d\'adresse de livraison.');
        }

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
                'status' => TransactionStatus::SHIPPING_INITIATED,
            ]);

            return redirect()->route('dashboard')->with('success', 'Envoi créé avec succès.');
        }

        return redirect()->route('dashboard')->with('error', 'Erreur lors de la création de l\'envoi.');
    }

    /**
     * Ajoute un numéro de suivi à une transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTracking(Request $request, Transaction $transaction)
    {
        // On s'assure que l'utilisateur connecté est bien le vendeur de l'article concerné
        $this->authorize('update', $transaction->offer->item);

        // Valider la requête
        $request->validate([
            'tracking_code' => 'required|string|max:255',
        ]);

        // Mettre à jour la transaction avec le numéro de suivi et le nouveau statut
        $transaction->update([
            'tracking_code' => $request->tracking_code,
            'status' => TransactionStatus::IN_TRANSIT,
        ]);

        return redirect()->route('transactions.sales')->with('success', 'Numéro de suivi ajouté avec succès.');
    }
}
