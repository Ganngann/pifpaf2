<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pickup_addresses')) {
            // Migrate pickup addresses
            $pickupAddresses = DB::table('pickup_addresses')->get();
            foreach ($pickupAddresses as $address) {
                DB::table('addresses')->insert([
                    'user_id' => $address->user_id,
                    'type' => 'pickup',
                    'name' => $address->name,
                    'street' => $address->street,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                    'created_at' => $address->created_at,
                    'updated_at' => $address->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('shipping_addresses')) {
            // Migrate shipping addresses
            $shippingAddresses = DB::table('shipping_addresses')->get();
            foreach ($shippingAddresses as $address) {
                DB::table('addresses')->insert([
                    'user_id' => $address->user_id,
                    'type' => 'shipping',
                    'name' => $address->name,
                    'street' => $address->street,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                    'country' => $address->country,
                    'created_at' => $address->created_at,
                    'updated_at' => $address->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('addresses')->truncate();
    }
};
