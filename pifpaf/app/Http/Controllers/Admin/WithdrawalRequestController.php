<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\WalletHistory;
use App\Enums\WithdrawalRequestStatus;
use App\Mail\WithdrawalRequestApproved;
use App\Mail\WithdrawalRequestRejected;
use App\Mail\WithdrawalRequestPaid;
use App\Mail\WithdrawalRequestFailed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WithdrawalRequestController extends Controller
{
    /**
     * Affiche la liste des demandes de virement.
     */
    public function index()
    {
        $withdrawalRequests = WithdrawalRequest::with('user', 'bankAccount')->latest()->get();

        return view('admin.withdrawal-requests.index', compact('withdrawalRequests'));
    }

    /**
     * Approuve une demande de virement.
     */
    public function approve(WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->update(['status' => WithdrawalRequestStatus::APPROVED]);
        Mail::to($withdrawalRequest->user)->send(new WithdrawalRequestApproved($withdrawalRequest));

        return redirect()->route('admin.withdrawal-requests.index')->with('success', 'La demande a été approuvée.');
    }

    /**
     * Rejette une demande de virement.
     */
    public function reject(WithdrawalRequest $withdrawalRequest)
    {
        // Re-créditer le portefeuille de l'utilisateur
        $user = $withdrawalRequest->user;
        $user->wallet += $withdrawalRequest->amount;
        $user->save();

        // Mettre à jour le statut de la demande
        $withdrawalRequest->update(['status' => WithdrawalRequestStatus::REJECTED]);

        // Ajouter une entrée à l'historique du portefeuille
        WalletHistory::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $withdrawalRequest->amount,
            'description' => 'Remboursement de la demande de virement #' . $withdrawalRequest->id,
        ]);

        Mail::to($user)->send(new WithdrawalRequestRejected($withdrawalRequest));

        return redirect()->route('admin.withdrawal-requests.index')->with('success', 'La demande a été rejetée et les fonds remboursés.');
    }

    /**
     * Marque une demande comme payée.
     */
    public function pay(WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->update(['status' => WithdrawalRequestStatus::PAID]);

        Mail::to($withdrawalRequest->user)->send(new WithdrawalRequestPaid($withdrawalRequest));

        return redirect()->route('admin.withdrawal-requests.index')->with('success', 'La demande a été marquée comme payée.');
    }

    /**
     * Marque une demande comme échouée.
     */
    public function fail(WithdrawalRequest $withdrawalRequest)
    {
        // Re-créditer le portefeuille de l'utilisateur
        $user = $withdrawalRequest->user;
        $user->wallet += $withdrawalRequest->amount;
        $user->save();

        // Mettre à jour le statut de la demande
        $withdrawalRequest->update(['status' => WithdrawalRequestStatus::FAILED]);

        // Ajouter une entrée à l'historique du portefeuille
        WalletHistory::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $withdrawalRequest->amount,
            'description' => 'Échec de la demande de virement #' . $withdrawalRequest->id . '. Fonds remboursés.',
        ]);

        Mail::to($user)->send(new WithdrawalRequestFailed($withdrawalRequest));

        return redirect()->route('admin.withdrawal-requests.index')->with('success', 'La demande a été marquée comme échouée et les fonds remboursés.');
    }
}
