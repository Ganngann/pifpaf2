<?php

namespace App\Http\Controllers;

use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Affiche le portefeuille de l'utilisateur.
     */
    public function show()
    {
        $user = Auth::user();
        $walletHistories = WalletHistory::where('user_id', $user->id)->with('transaction')->latest()->get();

        return view('wallet.show', [
            'user' => $user,
            'walletHistories' => $walletHistories,
        ]);
    }

    /**
     * Gère le retrait du portefeuille.
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $amount = $request->input('amount');

        if ($user->wallet < $amount) {
            return redirect()->route('wallet.show')->with('error', 'Solde insuffisant.');
        }

        $user->wallet -= $amount;
        $user->save();

        WalletHistory::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => $amount,
            'description' => 'Retrait de fonds',
        ]);

        return redirect()->route('wallet.show')->with('success', 'Retrait effectué avec succès.');
    }
}
