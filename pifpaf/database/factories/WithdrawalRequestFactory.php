<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\BankAccount;
use App\Enums\WithdrawalRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WithdrawalRequest>
 */
class WithdrawalRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bank_account_id' => BankAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => WithdrawalRequestStatus::PENDING,
        ];
    }
}
