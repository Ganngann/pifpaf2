<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PaymentVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seller = User::factory()->create();
        $buyer = User::create([
            'name' => 'Jules',
            'email' => 'jules@example.com',
            'password' => Hash::make('password'),
        ]);
        $item = Item::factory()->create(['user_id' => $seller->id]);
        Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'accepted'
        ]);
    }
}
