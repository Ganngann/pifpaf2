<?php

namespace Tests\Feature\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionConfirmationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function buyer_can_confirm_reception_and_seller_gets_paid(): void
    {
        // 1. Arrange
        $seller = User::factory()->create(['wallet' => 0]);
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'paid',
        ]);
        $transaction = Transaction::factory()->create([
            'offer_id' => $offer->id,
            'amount' => $offer->amount,
            'status' => 'payment_received',
        ]);

        // 2. Act
        $response = $this->actingAs($buyer)
                         ->patch(route('transactions.confirm-reception', $transaction));

        // 3. Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Réception confirmée. Le vendeur a été payé.');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $seller->id,
            'wallet' => $offer->amount,
        ]);
    }

    #[Test]
    public function another_user_cannot_confirm_reception(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $anotherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'status' => 'paid']);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => 'payment_received']);

        $response = $this->actingAs($anotherUser)
                         ->patch(route('transactions.confirm-reception', $transaction));

        $response->assertStatus(403);
    }
}
