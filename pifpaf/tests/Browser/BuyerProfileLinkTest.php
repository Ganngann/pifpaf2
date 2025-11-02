<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BuyerProfileLinkTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     */
    public function test_seller_can_see_buyer_profile_link_on_dashboard(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'pending',
        ]);

        $this->browse(function (Browser $browser) use ($seller, $buyer, $offer) {
            $browser->loginAs($seller)
                    ->visit('/dashboard')
                    ->assertSee('Offres reçues :')
                    ->assertSee($buyer->name)
                    ->assertSeeLink($buyer->name)
                    ->clickLink($buyer->name)
                    ->assertPathIs('/profile/' . $buyer->id);
        });
    }
}
