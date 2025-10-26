<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OfferFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     */
    public function testOfferWorkflow(): void
    {
        // 1. Créer un vendeur et un acheteur
        $seller = User::factory()->create([
            'email' => 'seller@example.com',
        ]);
        $buyer = User::factory()->create([
            'email' => 'buyer@example.com',
        ]);

        // 2. Le vendeur a un article en vente
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'Super Article à Vendre',
            'price' => 100,
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $item, $seller) {
            // 3. L'acheteur se connecte et fait une offre
            $browser->loginAs($buyer)
                    ->visit(route('items.show', $item))
                    ->assertSee('Super Article à Vendre')
                    ->type('amount', '80')
                    ->press('Envoyer l\'offre')
                    ->assertPathIs('/items/' . $item->id)
                    ->assertSee('Votre offre a été envoyée avec succès.');

            // 4. Le vendeur se connecte et voit l'offre sur son tableau de bord
            $browser->loginAs($seller)
                    ->visit('/dashboard')
                    ->assertSee('Offres reçues')
                    ->assertSee($buyer->name)
                    ->assertSee('80,00 €');

            // 5. Le vendeur accepte l'offre
            $browser->press('Accepter')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Offre acceptée.')
                    ->assertDontSee('Offres reçues'); // La section disparaît car il n'y a plus d'offre "pending"
        });
    }
}
