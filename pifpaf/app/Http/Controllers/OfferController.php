<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Offer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'delivery_method' => 'required|in:pickup,delivery',
        ]);

        // Vérifier que l'utilisateur ne fait pas une offre sur son propre article
        if ($item->user_id == Auth::id()) {
            return back()->withErrors(['amount' => 'Vous ne pouvez pas faire d\'offre sur votre propre article.']);
        }

        Offer::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'delivery_method' => $request->delivery_method,
        ]);

        return redirect()->route('items.show', $item)->with('success', 'Votre offre a été envoyée avec succès.');
    }

    /**
     * Accepte une offre.
     */
    public function accept(Offer $offer)
    {
        // On s'assure que l'utilisateur connecté est bien le vendeur de l'article concerné
        $this->authorize('update', $offer->item);

        // Mettre à jour le statut de l'offre
        $offer->update(['status' => 'accepted']);

        // Refuser toutes les autres offres pour cet article
        Offer::where('item_id', $offer->item_id)
             ->where('id', '!=', $offer->id)
             ->update(['status' => 'rejected']);

        return redirect()->route('dashboard')->with('success', 'Offre acceptée ! L\'acheteur doit maintenant procéder au paiement.');
    }

    /**
     * Refuse une offre.
     */
    public function reject(Offer $offer)
    {
        // On s'assure que l'utilisateur connecté est bien le vendeur de l'article concerné
        $this->authorize('update', $offer->item);

        $offer->update(['status' => 'rejected']);

        return redirect()->route('dashboard')->with('success', 'Offre refusée.');
    }
}
