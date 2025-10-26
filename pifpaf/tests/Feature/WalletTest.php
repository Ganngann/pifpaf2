<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste si le portefeuille du vendeur est crédité après une vente.
     *
     * @return void
     */
    public function test_seller_wallet_is_credited_after_sale()
    {
        // Créer un vendeur et un acheteur
        $seller = User::factory()->create(['wallet' => 100.00]);
        $buyer = User::factory()->create();

        // Créer un article appartenant au vendeur
        $item = Item::factory()->create(['user_id' => $seller->id]);

        // Créer une offre acceptée de l'acheteur pour cet article
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 50.00,
            'status' => 'accepted',
        ]);

        // Simuler la connexion de l'acheteur
        $this->actingAs($buyer);

        // Simuler le paiement
        $response = $this->post(route('payment.store', $offer));

        // Vérifier la redirection
        $response->assertRedirect(route('dashboard'));

        // Recharger les données du vendeur depuis la base de données
        $seller->refresh();

        // Vérifier que le portefeuille du vendeur a été crédité
        // Le solde initial était de 100.00, l'offre est de 50.00, le nouveau solde doit être 150.00
        $this->assertEquals(150.00, $seller->wallet);
    }
}
