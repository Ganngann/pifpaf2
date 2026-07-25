<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function create(Transaction $transaction)
    {
        // Prevent IDOR: Ensure only the buyer can create a dispute
        abort_unless($transaction->offer->user_id === auth()->id(), 403, 'Unauthorized action.');

        return view('disputes.create', compact('transaction'));
    }

    public function store(Request $request, Transaction $transaction)
    {
        // Prevent IDOR: Ensure only the buyer can store a dispute
        abort_unless($transaction->offer->user_id === auth()->id(), 403, 'Unauthorized action.');

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
