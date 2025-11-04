<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OfferDeliveryMethodTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test to verify delivery method selection on offer.
     *
     * @return void
     */
    public function testDeliveryMethodSelectionOnOffer()
    {
        // 1. Créer un vendeur et un acheteur
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $address = \App\Models\PickupAddress::factory()->create(['user_id' => $seller->id]);

        // 2. Créer un article avec les deux options de livraison
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'pickup_available' => true,
            'delivery_available' => true,
            'pickup_address_id' => $address->id,
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $item) {
            // 3. Connecter l'acheteur
            $browser->loginAs($buyer)
                    // 4. Naviguer vers la page de l'article
                    ->visit('/items/' . $item->id)
                    // 5. Vérifier la présence des deux boutons radio
                    ->assertVisible('@delivery-method-pickup')
                    ->assertVisible('@delivery-method-delivery')
                    // 6. Sélectionner l'option "delivery"
                    ->radio('delivery_method_choice', 'delivery')
                    ->pause(500) // Laisse le temps à AlpineJS de réagir
                    // 7. Remplir un montant et soumettre
                    ->type('#amount', '10')
                    ->press('@submit-offer-button')
                    ->waitForText('Votre offre a été envoyée avec succès.');
        });

        // 8. Vérifier en base de données
        $this->assertDatabaseHas('offers', [
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'amount' => 10,
            'delivery_method' => 'delivery',
        ]);
    }
}
