<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\PickupAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeliveryOptionsDisplayTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test que les options de livraison s'affichent correctement sur la page de l'article.
     *
     * @return void
     */
    public function testDeliveryOptionsAreDisplayedCorrectly()
    {
        $user = User::factory()->create();
        $address = PickupAddress::factory()->create([
            'user_id' => $user->id,
            'city' => 'Lille'
        ]);

        // 1. Article avec retrait sur place uniquement
        $itemWithPickup = Item::factory()->create([
            'user_id' => $user->id,
            'pickup_available' => true,
            'delivery_available' => false,
            'pickup_address_id' => $address->id,
        ]);

        // 2. Article avec livraison uniquement
        $itemWithDelivery = Item::factory()->create([
            'user_id' => $user->id,
            'pickup_available' => false,
            'delivery_available' => true,
            'pickup_address_id' => null,
        ]);

        // 3. Article avec les deux options
        $itemWithBoth = Item::factory()->create([
            'user_id' => $user->id,
            'pickup_available' => true,
            'delivery_available' => true,
            'pickup_address_id' => $address->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $itemWithPickup, $itemWithDelivery, $itemWithBoth, $address) {
            // Test du premier article (retrait uniquement)
            $browser->loginAs($user)
                    ->visit('/items/' . $itemWithPickup->id)
                    ->assertSee('Modes de livraison')
                    ->assertSee('Retrait sur place')
                    ->assertSee('À ' . $address->city)
                    ->assertDontSee('Livraison');

            // Test du deuxième article (livraison uniquement)
            $browser->visit('/items/' . $itemWithDelivery->id)
                    ->assertSee('Modes de livraison')
                    ->assertDontSee('Retrait sur place')
                    ->assertSee('Livraison');

            // Test du troisième article (les deux)
            $browser->visit('/items/' . $itemWithBoth->id)
                    ->assertSee('Modes de livraison')
                    ->assertSee('Retrait sur place')
                    ->assertSee('À ' . $address->city)
                    ->assertSee('Livraison');
        });
    }
}
