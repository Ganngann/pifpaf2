<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word,
            'street' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'is_for_pickup' => true,
            'is_for_delivery' => false,
        ];
    }

    /**
     * Indicate that the address is for delivery.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function delivery(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_for_pickup' => false,
                'is_for_delivery' => true,
            ];
        });
    }

    /**
     * Indicate that the address is for both pickup and delivery.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pickupAndDelivery(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_for_pickup' => true,
                'is_for_delivery' => true,
            ];
        });
    }
}
