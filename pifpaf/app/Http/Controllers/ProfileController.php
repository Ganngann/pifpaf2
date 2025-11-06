<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Export the user's data.
     */
    public function export(Request $request)
    {
        $user = $request->user();
        $user->load('items', 'offers', 'pickupAddresses', 'shippingAddresses');

        // Récupérer les transactions de vente
        $salesTransactions = Transaction::whereHas('offer.item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Récupérer les transactions d'achat
        $purchaseTransactions = Transaction::whereHas('offer', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        $data = [
            'profile' => $user->toArray(),
            'salesTransactions' => $salesTransactions->toArray(),
            'purchaseTransactions' => $purchaseTransactions->toArray(),
        ];

        $fileName = 'pifpaf_user_data_' . $user->id . '.json';
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return response()->json($data, 200, $headers);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('reviewsReceived.reviewer');
        $items = $user->items()->available()->with('primaryImage')->latest()->paginate(9);

        $averageRating = $user->reviewsReceived->avg('rating');
        $reviewCount = $user->reviewsReceived->count();

        return view('profile.show', compact('user', 'items', 'averageRating', 'reviewCount'));
    }
}
