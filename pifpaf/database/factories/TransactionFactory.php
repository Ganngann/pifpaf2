<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Offer;
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
            'offer_id' => Offer::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'status' => 'completed',
            'address_id' => function (array $attributes) {
                // Ensure the address belongs to the user who made the offer (the buyer).
                $offer = Offer::find($attributes['offer_id']);
                return Address::factory()->delivery()->create([
                    'user_id' => $offer->user_id,
                ]);
            },
            'sendcloud_parcel_id' => null,
            'tracking_code' => null,
            'label_url' => null,
        ];
    }
}
