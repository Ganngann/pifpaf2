<?php

namespace App\Http\Controllers;

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
        return view('wallet.show', ['user' => $user]);
    }
}
