<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\ShippingAddress;
use App\Enums\ItemStatus;

class CompletedSalesSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'test@example.com')->first();
        if (!$seller) {
            $seller = User::factory()->create(['email' => 'test@example.com']);
        }

        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'status' => ItemStatus::SOLD]);
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $buyer->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'status' => 'paid']);
        Transaction::factory()->create(['offer_id' => $offer->id, 'status' => 'completed', 'shipping_address_id' => $shippingAddress->id]);
    }
}
