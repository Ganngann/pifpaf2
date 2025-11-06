<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function create(Transaction $transaction)
    {
        return view('disputes.create', compact('transaction'));
    }

    public function store(Request $request, Transaction $transaction)
    {
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
