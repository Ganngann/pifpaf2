<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_bank_accounts()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('profile.bank-accounts.index'));
        $response->assertStatus(200);
        $response->assertViewIs('profile.bank-accounts.index');
    }

    public function test_user_can_create_bank_account()
    {
        $user = User::factory()->create();
        $data = ['account_holder_name' => 'John Doe', 'iban' => 'FR7612345678901234567890123', 'bic' => 'ABCDEFGH'];
        $response = $this->actingAs($user)->post(route('profile.bank-accounts.store'), $data);
        $response->assertRedirect(route('profile.bank-accounts.index'));
        $this->assertDatabaseHas('bank_accounts', array_merge($data, ['user_id' => $user->id]));
    }

    public function test_user_can_update_bank_account()
    {
        $user = User::factory()->create();
        $account = BankAccount::factory()->create(['user_id' => $user->id]);
        $data = ['account_holder_name' => 'Jane Doe', 'iban' => 'FR7612345678901234567890123', 'bic' => 'ABCDEFGH'];
        $response = $this->actingAs($user)->put(route('profile.bank-accounts.update', $account), $data);
        $response->assertRedirect(route('profile.bank-accounts.index'));
        $this->assertDatabaseHas('bank_accounts', array_merge($data, ['id' => $account->id]));
    }

    public function test_user_can_delete_bank_account()
    {
        $user = User::factory()->create();
        $account = BankAccount::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->delete(route('profile.bank-accounts.destroy', $account));
        $response->assertRedirect(route('profile.bank-accounts.index'));
        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }

    public function test_user_can_view_create_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('profile.bank-accounts.create'));
        $response->assertStatus(200);
        $response->assertViewIs('profile.bank-accounts.create');
    }

    public function test_user_can_view_edit_page()
    {
        $user = User::factory()->create();
        $account = BankAccount::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get(route('profile.bank-accounts.edit', $account));
        $response->assertStatus(200);
        $response->assertViewIs('profile.bank-accounts.edit');
    }

    public function test_user_cannot_view_other_users_bank_account()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $account = BankAccount::factory()->create(['user_id' => $otherUser->id]);
        $response = $this->actingAs($user)->get(route('profile.bank-accounts.edit', $account));
        $response->assertStatus(403);
    }
}
