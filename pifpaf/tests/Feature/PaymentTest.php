<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_can_pay_for_an_accepted_offer(): void
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'accepted',
        ]);

        // 2. Act
        $response = $this->actingAs($buyer)->post(route('payment.store', $offer));

        // 3. Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Paiement effectué avec succès ! Votre commande est en attente de confirmation de réception.');

        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id,
            'amount' => $offer->amount,
            'status' => 'payment_received',
        ]);

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }
}
