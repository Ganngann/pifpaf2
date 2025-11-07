<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Display the order summary page.
     *
     * @param  \App\Models\Offer  $offer
     * @return \Illuminate\View\View
     */
    public function summary(Offer $offer)
    {
        // Ensure the authenticated user is the buyer.
        if (Auth::id() !== $offer->user_id) {
            abort(403, 'Unauthorized access.');
        }

        // Ensure the offer has been accepted.
        if ($offer->status !== 'accepted') {
            return redirect()->route('dashboard')->withErrors(['error' => 'This offer is not ready for checkout.']);
        }

        $shippingAddress = Auth::user()->shippingAddresses()->first();

        return view('checkout.summary', compact('offer', 'shippingAddress'));
    }
}
