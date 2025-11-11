<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $bankAccounts = $user->bankAccounts;

        return view('profile.bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('profile.bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34', // Assuming IBAN max length
            'bic' => 'required|string|max:11', // Assuming BIC max length
        ]);

        Auth::user()->bankAccounts()->create($request->all());

        return redirect()->route('profile.bank-accounts.index')->with('success', 'Compte bancaire ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $this->authorize('view', $bankAccount);
        return view('profile.bank-accounts.show', compact('bankAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        return view('profile.bank-accounts.edit', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);

        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'bic' => 'required|string|max:11',
        ]);

        $bankAccount->update($request->all());

        return redirect()->route('profile.bank-accounts.index')->with('success', 'Compte bancaire mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);
        $bankAccount->delete();

        return redirect()->route('profile.bank-accounts.index')->with('success', 'Compte bancaire supprimé avec succès.');
    }
}
