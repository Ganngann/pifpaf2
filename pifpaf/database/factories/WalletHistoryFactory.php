<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletHistory>
 */
class WalletHistoryFactory extends Factory
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
            'type' => $this->faker->randomElement(['credit', 'debit', 'withdrawal']),
            'amount' => $this->faker->numberBetween(10, 100),
            'description' => $this->faker->sentence,
        ];
    }
}
