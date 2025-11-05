<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;
use Mockery;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function seller_wallet_is_credited_after_sale(): void
    {
        $seller = User::factory()->create(['wallet' => 100.00]);
        $buyer = User::factory()->create(['wallet' => 50.00]); // L'acheteur a assez pour payer
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 50.00]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'amount' => 50.00,
            'status' => 'accepted'
        ]);

        $this->actingAs($buyer);

        // L'acheteur choisit de payer avec son portefeuille
        $response = $this->post(route('payment.store', $offer), [
            'use_wallet' => true
        ]);

        // Vérifier la redirection
        $response->assertRedirect(route('dashboard'));

        // Recharger les données du vendeur depuis la base de données pour vérifier qu'il n'est PAS encore crédité
        $seller->refresh();
        $this->assertEquals(100.00, $seller->wallet);

        // L'acheteur confirme la réception de l'article
        $transaction = Transaction::where('offer_id', $offer->id)->first();
        $this->actingAs($buyer)->patch(route('transactions.confirm-reception', $transaction));

        // Recharger les données du vendeur pour vérifier que son portefeuille EST crédité
        $seller->refresh();
        $this->assertEquals(150.00, $seller->wallet);

        // Vérifier que le portefeuille de l'acheteur a été débité
        $buyer->refresh();
        $this->assertEquals(0, $buyer->wallet);
    }
}
