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
        // 🛡️ Security: Prevent IDOR - Ensure user is authorized to view this transaction
        // Only buyers and sellers of this transaction can create a dispute
        $this->authorize('view', $transaction);

        return view('disputes.create', compact('transaction'));
    }

    public function store(Request $request, Transaction $transaction)
    {
        // 🛡️ Security: Prevent IDOR - Ensure user is authorized to interact with this transaction
        // Only buyers and sellers of this transaction can store a dispute
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
