<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Enums\ItemStatus;
use App\Enums\TransactionStatus;

class ConfirmReceptionPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;
    private User $otherUser;
    private Item $item;
    private Offer $offer;
    private Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create();
        $this->buyer = User::factory()->create();
        $this->otherUser = User::factory()->create();

        $this->item = Item::factory()->create([
            'user_id' => $this->seller->id,
            'status' => ItemStatus::SOLD,
        ]);

        $this->offer = Offer::factory()->create([
            'item_id' => $this->item->id,
            'user_id' => $this->buyer->id,
        ]);

        $this->transaction = Transaction::factory()->create([
            'offer_id' => $this->offer->id,
            'status' => 'payment_received',
        ]);
    }

    public function test_buyer_can_confirm_reception(): void
    {
        $response = $this->actingAs($this->buyer)
            ->patch(route('transactions.confirm-reception', $this->transaction));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('transactions', [
            'id' => $this->transaction->id,
            'status' => 'completed',
        ]);
    }

    public function test_seller_cannot_confirm_reception(): void
    {
        $response = $this->actingAs($this->seller)
            ->patch(route('transactions.confirm-reception', $this->transaction));

        $response->assertStatus(403);
        $this->assertDatabaseHas('transactions', [
            'id' => $this->transaction->id,
            'status' => 'payment_received',
        ]);
    }

    public function test_other_user_cannot_confirm_reception(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->patch(route('transactions.confirm-reception', $this->transaction));

        $response->assertStatus(403);
        $this->assertDatabaseHas('transactions', [
            'id' => $this->transaction->id,
            'status' => 'payment_received',
        ]);
    }
}
