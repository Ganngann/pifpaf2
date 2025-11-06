<?php

namespace Tests\Feature\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function payment_creates_transaction_with_payment_received_status_and_does_not_pay_seller(): void
    {
        // 1. Arrange
        $seller = User::factory()->create(['wallet' => 0]);
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'accepted',
        ]);

        // Simuler l'API Stripe
        Mockery::mock('overload:\Stripe\PaymentIntent')->shouldReceive('retrieve')->andReturn((object) [
            'status' => 'succeeded',
            'amount' => round($offer->amount * 100),
        ]);

        // 2. Act
        $response = $this->actingAs($buyer)
                         ->post(route('payment.store', $offer), [
                             'payment_intent_id' => 'pi_mock_id',
                             'use_wallet' => false,
                         ]);

        // 3. Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id,
            'status' => 'payment_received',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $seller->id,
            'wallet' => 0, // Le portefeuille du vendeur ne doit pas être crédité
        ]);
    }
}
