<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste le flux de paiement complet utilisant le portefeuille et la carte.
     *
     * @return void
     */
    public function test_payment_flow_with_wallet_and_card_is_successful()
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 50.00]); // L'acheteur a 50€ dans son portefeuille
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 120.00]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'amount' => 100.00, // L'offre est de 100€
            'status' => 'accepted',
        ]);

        $this->actingAs($buyer);

        // 2. Act
        $response = $this->post(route('payment.store', $offer), [
            'use_wallet' => true,
            'wallet_amount' => 50.00, // On utilise les 50€ du portefeuille
            'card_amount' => 50.00,   // On paie 50€ par carte
        ]);

        // 3. Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Vérifier que le portefeuille de l'acheteur a été correctement mis à jour
        $buyer->refresh();
        // Solde initial: 50€. Paiement par carte: +50€ -> 100€. Achat: -100€ -> 0€.
        $this->assertEquals(0.00, $buyer->wallet);

        // Vérifier qu'une transaction a été créée avec les bonnes données
        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id,
            'amount' => 100.00,
            'wallet_amount' => 50.00,
            'card_amount' => 50.00,
            'status' => 'payment_received',
        ]);

        // Vérifier que le statut de l'offre et de l'article ont été mis à jour
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
