<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingAddress>
 */
class ShippingAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->name,
            'street' => $this->faker->streetAddress,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'country' => $this->faker->countryCode,
        ];
    }
}
