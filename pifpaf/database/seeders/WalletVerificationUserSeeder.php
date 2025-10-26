<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WalletVerificationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'test-wallet@example.com'],
            [
                'name' => 'Test Wallet User',
                'password' => Hash::make('password'),
                'wallet' => 250.75,
            ]
        );
    }
}
