<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administration.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userCount = User::count();
        $itemCount = Item::count();
        $transactionCount = Transaction::count();

        return view('admin.dashboard', compact('userCount', 'itemCount', 'transactionCount'));
    }
}
