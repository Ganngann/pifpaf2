<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Mettre à jour les préférences de notification
        $preferences = $request->input('notification_preferences', []);
        $user->notification_preferences = [
            'new_offer' => isset($preferences['new_offer']),
            'offer_accepted' => isset($preferences['offer_accepted']),
            'offer_rejected' => isset($preferences['offer_rejected']),
            'payment_received' => isset($preferences['payment_received']),
            'shipment' => isset($preferences['shipment']),
            'reception_confirmed' => isset($preferences['reception_confirmed']),
            'new_message' => isset($preferences['new_message']),
            'confirmation_reminder' => isset($preferences['confirmation_reminder']),
        ];

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('reviewsReceived.reviewer');
        $items = $user->items()->available()->with(['designatedPrimaryImage', 'images'])->latest()->paginate(8);

        $averageRating = $user->reviewsReceived->avg('rating');
        $reviewCount = $user->reviewsReceived->count();

        return view('profile.show', compact('user', 'items', 'averageRating', 'reviewCount'));
    }

    /**
     * Export the user's data.
     */
    public function export(Request $request)
    {
        $user = $request->user();

        $data = [
            'profile' => $user->toArray(),
            'items' => $user->items->toArray(),
            'offers' => $user->offers->toArray(),
            'pickup_addresses' => $user->pickupAddresses->toArray(),
            'shipping_addresses' => $user->shippingAddresses->toArray(),
            'reviews_written' => $user->reviewsWritten->toArray(),
            'reviews_received' => $user->reviewsReceived->toArray(),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT);

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="pifpaf_user_data.json"',
        ]);
    }
}
