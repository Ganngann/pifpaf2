<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\WithdrawalRequest;
use App\Enums\WithdrawalRequestStatus;
use App\Mail\WithdrawalRequestApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WithdrawalRequestManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_approve_a_withdrawal_request()
    {
        // 1. Arrange
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['wallet' => 100]);
        $bankAccount = BankAccount::factory()->create(['user_id' => $seller->id]);
        $withdrawalRequest = WithdrawalRequest::factory()->create([
            'user_id' => $seller->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => 50,
            'status' => WithdrawalRequestStatus::PENDING,
        ]);

        Mail::fake();

        // 2. Act
        $response = $this->actingAs($admin)
                         ->post(route('admin.withdrawal-requests.approve', $withdrawalRequest));

        // 3. Assert
        $response->assertRedirect(route('admin.withdrawal-requests.index'));
        $response->assertSessionHas('success', 'La demande a été approuvée.');

        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawalRequest->id,
            'status' => WithdrawalRequestStatus::APPROVED->value,
        ]);

        Mail::assertSent(WithdrawalRequestApproved::class, function ($mail) use ($seller) {
            return $mail->hasTo($seller->email);
        });
    }

    /** @test */
    public function an_admin_can_reject_a_withdrawal_request_and_funds_are_refunded()
    {
        // 1. Arrange
        $admin = User::factory()->create(['role' => 'admin']);
        $initialWalletBalance = 100;
        $withdrawalAmount = 50;
        $seller = User::factory()->create(['wallet' => $initialWalletBalance]);
        $bankAccount = BankAccount::factory()->create(['user_id' => $seller->id]);
        $withdrawalRequest = WithdrawalRequest::factory()->create([
            'user_id' => $seller->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => $withdrawalAmount,
            'status' => WithdrawalRequestStatus::PENDING,
        ]);

        // Simulate freezing funds
        $seller->wallet -= $withdrawalAmount;
        $seller->save();

        Mail::fake();

        // 2. Act
        $response = $this->actingAs($admin)
                         ->post(route('admin.withdrawal-requests.reject', $withdrawalRequest));

        // 3. Assert
        $response->assertRedirect(route('admin.withdrawal-requests.index'));
        $response->assertSessionHas('success', 'La demande a été rejetée et les fonds remboursés.');

        $this->assertDatabaseHas('withdrawal_requests', [
            'id' => $withdrawalRequest->id,
            'status' => WithdrawalRequestStatus::REJECTED->value,
        ]);

        $this->assertEquals($initialWalletBalance, $seller->fresh()->wallet);
    }
}
