<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Vérifier que la transaction est terminée
        if ($transaction->status !== 'completed') {
            return back()->with('error', 'Vous ne pouvez laisser un avis que pour une transaction terminée.');
        }

        // Déterminer qui est le reviewer et le reviewee
        $user = Auth::user();
        $buyer = $transaction->offer->user;
        $seller = $transaction->offer->item->user;

        if ($user->id !== $buyer->id && $user->id !== $seller->id) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à laisser un avis pour cette transaction.');
        }

        $reviewee = ($user->id === $buyer->id) ? $seller : $buyer;

        // Vérifier qu'un avis n'a pas déjà été laissé par cet utilisateur pour cette transaction
        if ($transaction->reviews()->where('reviewer_id', $user->id)->exists()) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour cette transaction.');
        }

        // Créer l'avis
        $transaction->reviews()->create([
            'reviewer_id' => $user->id,
            'reviewee_id' => $reviewee->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre avis a bien été enregistré.');
    }
}
