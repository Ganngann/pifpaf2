<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class PaymentFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function an_buyer_can_see_and_pay_for_an_accepted_offer()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                ->visit('/dashboard')
                ->assertSee('Mes offres')
                ->assertSee($offer->item->title)
                ->clickLink('Payer')
                ->assertPathIs('/payment/' . $offer->id)
                ->assertSee('Récapitulatif de la commande')
                ->waitFor('#card_number')
                ->type('#card_number', '1234567812345678')
                ->type('#expiry_date', '12/25')
                ->type('#cvc', '123')
                ->click('@submit-payment-button')
                ->assertPathIs('/dashboard')
                ->assertSee('Paiement effectué avec succès !');
        });
    }
}
