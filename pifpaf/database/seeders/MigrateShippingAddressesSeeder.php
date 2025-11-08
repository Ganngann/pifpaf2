<?php

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\ShippingAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateShippingAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting migration of shipping_addresses to addresses table...');

        $shippingAddresses = ShippingAddress::all();

        foreach ($shippingAddresses as $shippingAddress) {
            Address::create([
                'user_id' => $shippingAddress->user_id,
                'type' => AddressType::DELIVERY,
                'name' => $shippingAddress->name,
                'street' => $shippingAddress->street,
                'city' => $shippingAddress->city,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
                // Latitude and longitude are nullable, so they are omitted
            ]);
        }

        $this->command->info('Successfully migrated ' . $shippingAddresses->count() . ' shipping addresses.');
    }
}
