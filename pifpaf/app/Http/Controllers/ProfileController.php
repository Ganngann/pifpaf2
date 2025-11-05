<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
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
