<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    use AuthorizesRequests;

    public function create(Transaction $transaction)
    {
        // Security Fix: Prevent IDOR by ensuring only transaction participants can create a dispute
        $this->authorize('view', $transaction);

        return view('disputes.create', compact('transaction'));
    }

    public function store(Request $request, Transaction $transaction)
    {
        // Security Fix: Prevent IDOR by ensuring only transaction participants can create a dispute
        $this->authorize('view', $transaction);

        $request->validate([
            'reason' => 'required|string|min:20',
        ]);

        $transaction->dispute()->create([
            'user_id' => auth()->id(),
            'reason' => $request->reason,
        ]);

        $transaction->update(['status' => TransactionStatus::DISPUTED]);

        return redirect()->route('transactions.show', $transaction)->with('success', 'Litige ouvert avec succès.');
    }
}
