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
        $user->load('items', 'reviewsReceived.reviewer');

        $averageRating = $user->reviewsReceived->avg('rating');
        $reviewCount = $user->reviewsReceived->count();

        return view('profile.show', compact('user', 'averageRating', 'reviewCount'));
    }
}
