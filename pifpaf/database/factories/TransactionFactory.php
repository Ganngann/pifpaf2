<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => \App\Models\Offer::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'status' => 'completed',
            'shipping_address_id' => null,
            'sendcloud_parcel_id' => null,
            'tracking_code' => null,
            'label_url' => null,
        ];
    }
}
