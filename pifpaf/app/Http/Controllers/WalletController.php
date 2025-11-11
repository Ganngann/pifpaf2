<?php

namespace App\Http\Controllers;

use App\Models\WalletHistory;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    /**
     * Affiche le portefeuille de l'utilisateur.
     */
    public function show()
    {
        $user = Auth::user();
        $walletHistories = WalletHistory::where('user_id', $user->id)->with('transaction')->latest()->get();
        $bankAccounts = $user->bankAccounts;
        $withdrawalRequests = $user->withdrawalRequests()->latest()->get();

        return view('wallet.show', [
            'user' => $user,
            'walletHistories' => $walletHistories,
            'bankAccounts' => $bankAccounts,
            'withdrawalRequests' => $withdrawalRequests,
        ]);
    }

    /**
     * Gère la demande de virement.
     */
    public function withdraw(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_account_id' => [
                'required',
                Rule::exists('bank_accounts', 'id')->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }),
            ],
        ]);

        $amount = $request->input('amount');
        $bankAccountId = $request->input('bank_account_id');

        try {
            DB::transaction(function () use ($user, $amount, $bankAccountId) {
                // Verrouiller l'utilisateur pour éviter les race conditions
                $user = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();

                if ($user->wallet < $amount) {
                    throw new \Exception('Solde insuffisant.');
                }

                // Geler les fonds en les déduisant du portefeuille principal
                $user->wallet -= $amount;
                $user->save();

                // Créer la demande de virement
                $withdrawalRequest = WithdrawalRequest::create([
                    'user_id' => $user->id,
                    'bank_account_id' => $bankAccountId,
                    'amount' => $amount,
                    'status' => 'pending',
                ]);

                // Créer une entrée dans l'historique du portefeuille
                WalletHistory::create([
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $amount,
                    'description' => 'Demande de virement #' . $withdrawalRequest->id,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->route('wallet.show')->with('error', $e->getMessage());
        }

        return redirect()->route('wallet.show')->with('success', 'Votre demande de virement a bien été enregistrée.');
    }
}
