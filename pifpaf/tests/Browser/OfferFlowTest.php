<?php

namespace Tests\Browser;

use App\Models\Address;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class OfferFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function testOfferWorkflow(): void
    {
        // 1. Créer un vendeur et un acheteur
        $seller = User::factory()->create([
            'email' => 'seller@example.com',
        ]);
        $buyer = User::factory()->create([
            'email' => 'buyer@example.com',
        ]);
        $address = Address::factory()->create(['user_id' => $seller->id]);

        // 2. Le vendeur a un article en vente
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'Super Article à Vendre',
            'price' => 100,
            'delivery_available' => true,
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $item, $seller) {
            // 3. L'acheteur se connecte et fait une offre
            $browser->loginAs($buyer)
                    ->visit(route('items.show', $item))
                    ->assertSeeIn('@item-title', 'Super Article à Vendre')
                    ->radio('delivery_method_choice', 'delivery') // Sélectionner la livraison
                    ->type('#amount', '80')
                    ->press('@submit-offer-button')
                    ->waitForText('Votre offre a été envoyée avec succès.') // Wait for flash message
                    ->assertSee('Votre offre a été envoyée avec succès.');

            // 4. Le vendeur se connecte et voit l'offre sur son tableau de bord
            $browser->loginAs($seller)
                    ->visit('/dashboard')
                    ->waitForText('Offres reçues')
                    ->assertSee($buyer->name)
                    ->assertSee('80,00 €')
                    ->assertSee('Livraison');

            // 5. Le vendeur accepte l'offre
            $browser->press('Accepter')
                    ->waitForText('Offre acceptée ! L\'acheteur doit maintenant procéder au paiement.') // Wait for the correct flash message
                    ->assertSee('Offre acceptée ! L\'acheteur doit maintenant procéder au paiement.')
                    ->assertPathIs('/dashboard')
                    ->assertDontSee('Offres reçues'); // La section disparaît car il n'y a plus d'offre "pending"
        });
    }
}
