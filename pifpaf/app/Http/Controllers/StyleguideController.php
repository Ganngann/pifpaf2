<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StyleguideController extends Controller
{
    public function index()
    {
        // --- User & Item Setup ---
        $seller = User::firstOrCreate(
            ['email' => 'seller-styleguide@example.com'],
            array_merge(User::factory()->make()->toArray(), ['password' => Hash::make('password')])
        );
        $buyer = User::firstOrCreate(
            ['email' => 'buyer-styleguide@example.com'],
            array_merge(User::factory()->make()->toArray(), ['password' => Hash::make('password')])
        );

        $itemWithImage = Item::where('user_id', $seller->id)->has('images')->with('primaryImage', 'user')->first();
        if (!$itemWithImage) {
            $itemWithImage = Item::factory()->hasImages(1)->create(['user_id' => $seller->id]);
        }

        $itemWithoutImage = Item::where('user_id', $seller->id)->doesntHave('images')->first();
        if (!$itemWithoutImage) {
            $itemWithoutImage = Item::factory()->create(['user_id' => $seller->id]);
        }

        // --- Offer Setup ---
        $offerPending = Offer::factory()->create([
            'item_id' => $itemWithImage->id,
            'user_id' => $buyer->id,
            'status' => 'pending'
        ]);
        $offerAccepted = Offer::factory()->create([
            'item_id' => $itemWithImage->id,
            'user_id' => $buyer->id,
            'status' => 'accepted'
        ]);

        // --- Review Setup (requires a full transaction) ---
        $transactionForReview = Transaction::factory()->create([
            'offer_id' => Offer::factory()->create([
                'item_id' => $itemWithImage->id,
                'user_id' => $buyer->id,
                'status' => 'paid'
            ])->id,
            'status' => 'completed'
        ]);
        $review = Review::factory()->create([
            'transaction_id' => $transactionForReview->id,
            'reviewer_id' => $buyer->id,
            'reviewee_id' => $seller->id,
        ]);

        // --- Wallet History Setup ---
        $walletCredit = WalletHistory::factory()->create(['user_id' => $seller->id, 'type' => 'credit']);
        $walletDebit = WalletHistory::factory()->create(['user_id' => $seller->id, 'type' => 'debit']);
        $walletWithdrawal = WalletHistory::factory()->create(['user_id' => $seller->id, 'type' => 'withdrawal']);


        return view('styleguide', [
            'itemWithImage' => $itemWithImage,
            'itemWithoutImage' => $itemWithoutImage,
            'testUser' => $seller,
            'offerPending' => $offerPending,
            'offerAccepted' => $offerAccepted,
            'review' => $review,
            'walletCredit' => $walletCredit,
            'walletDebit' => $walletDebit,
            'walletWithdrawal' => $walletWithdrawal,
        ]);
    }
}
