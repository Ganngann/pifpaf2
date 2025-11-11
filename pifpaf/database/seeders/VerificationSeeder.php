<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\Address;
use App\Enums\ItemStatus;
use App\Enums\TransactionStatus;

class VerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seller = User::factory()->create(['email' => 'seller-verif@example.com']);
        $buyer = User::factory()->create();
        $address = Address::factory()->for($buyer)->create([
            'is_for_delivery' => true,
            'country' => 'FR',
        ]);
        $item = Item::factory()->for($seller)->create([
            'status' => ItemStatus::SOLD,
            'weight' => 1000,
        ]);
        $offer = Offer::factory()->for($item)->for($buyer)->create();
        $transaction = Transaction::factory()->for($offer)->create([
            'status' => TransactionStatus::PAYMENT_RECEIVED,
            'address_id' => $address->id
        ]);

        // Output the transaction ID for the verification script
        echo "TRANSACTION_ID:" . $transaction->id;
    }
}
