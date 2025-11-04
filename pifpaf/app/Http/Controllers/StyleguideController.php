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
            [
                'name' => 'Styleguide Seller',
                'password' => Hash::make('password'),
                'wallet' => 1000,
            ]
        );
        $buyer = User::firstOrCreate(
            ['email' => 'buyer-styleguide@example.com'],
            [
                'name' => 'Styleguide Buyer',
                'password' => Hash::make('password'),
                'wallet' => 500,
            ]
        );

        // --- Item Setup ---
        $pickupAddress = \App\Models\PickupAddress::firstOrCreate(
            ['user_id' => $seller->id, 'street' => '123 Styleguide St'],
            [
                'name' => 'Maison (Styleguide)',
                'city' => 'Styleville',
                'postal_code' => 'S1G 1T1',
                'country' => 'Guideland',
            ]
        );

        $itemDataDefaults = [
            'description' => 'A default description for a styleguide item.',
            'price' => 99.99,
            'category' => 'Vêtements',
            'status' => \App\Enums\ItemStatus::AVAILABLE,
            'pickup_available' => true,
            'delivery_available' => false,
            'user_id' => $seller->id,
            'pickup_address_id' => $pickupAddress->id,
        ];

        $itemWithImage = Item::firstOrCreate(
            ['title' => 'Styleguide: Item With Image'],
            $itemDataDefaults
        );

        if ($itemWithImage->wasRecentlyCreated) {
            \App\Models\ItemImage::create([
                'item_id' => $itemWithImage->id,
                'path' => 'https://placehold.co/600x400', // Use a placeholder URL
                'is_primary' => true,
                'order' => 1,
            ]);
        }
        $itemWithImage->load('primaryImage', 'user', 'images');

        $itemWithoutImage = Item::firstOrCreate(
            ['title' => 'Styleguide: Item Without Image'],
            $itemDataDefaults
        );
        $itemWithoutImage->load('user');

        // --- Offer Setup ---
        $offerPending = Offer::firstOrCreate(
            ['item_id' => $itemWithImage->id, 'user_id' => $buyer->id, 'status' => 'pending'],
            ['amount' => $itemWithImage->price] // Provide explicit data, avoid factory
        );
        $offerAccepted = Offer::firstOrCreate(
            ['item_id' => $itemWithImage->id, 'user_id' => $buyer->id, 'status' => 'accepted'],
            ['amount' => $itemWithImage->price] // Provide explicit data, avoid factory
        );

        // --- Review Setup (requires a full transaction) ---
        $paidOffer = Offer::firstOrCreate(
            ['item_id' => $itemWithImage->id, 'user_id' => $buyer->id, 'status' => 'paid'],
            ['amount' => $itemWithImage->price] // Provide explicit data, avoid factory
        );
        $transactionForReview = Transaction::firstOrCreate(
            ['offer_id' => $paidOffer->id, 'status' => 'completed'],
            [ // Provide explicit data, avoid factory
                'amount' => $paidOffer->amount,
                'wallet_amount' => $paidOffer->amount,
                'card_amount' => 0,
            ]
        );
        $review = Review::firstOrCreate(
            ['transaction_id' => $transactionForReview->id, 'reviewer_id' => $buyer->id],
            [ // Provide explicit data, avoid factory
                'reviewee_id' => $seller->id,
                'rating' => 5,
                'comment' => 'Excellent seller for the styleguide!',
            ]
        );

        // --- Wallet History Setup ---
        $walletCredit = WalletHistory::firstOrCreate(
            ['user_id' => $seller->id, 'type' => 'credit'],
            [ // Provide explicit data, avoid factory
                'amount' => 150.75,
                'description' => 'Styleguide credit example',
                'transaction_id' => $transactionForReview->id
            ]
        );
        $walletDebit = WalletHistory::firstOrCreate(
            ['user_id' => $seller->id, 'type' => 'debit'],
            [ // Provide explicit data, avoid factory
                'amount' => -25.50,
                'description' => 'Styleguide debit example',
                'transaction_id' => null
            ]
        );
        $walletWithdrawal = WalletHistory::firstOrCreate(
            ['user_id' => $seller->id, 'type' => 'withdrawal'],
            [ // Provide explicit data, avoid factory
                'amount' => -100.00,
                'description' => 'Styleguide withdrawal example',
                'transaction_id' => null
            ]
        );


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
