<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use PHPUnit\Framework\Attributes\Test;


class PaymentTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function a_user_can_pay_partially_with_wallet()
    {
        $this->markTestSkipped('Les tests de paiement sont désactivés pour éviter les transactions parasites.');
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 5.00]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 20.00]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 15.00,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                    ->visit(route('payment.create', $offer))
                    ->assertSee('Utiliser mon solde de portefeuille (5,00 €)')
                    ->check('use_wallet')
                    ->assertSee('Total à payer : 10.00 €')
                    ->type('#card_number', '1234567812345678')
                    ->type('#expiry_date', '12/25')
                    ->type('#cvc', '123')
                    ->click('@submit-payment-button')
                    ->waitForText('Paiement effectué avec succès')
                    ->assertPathIs('/dashboard');

            $this->assertDatabaseHas('transactions', [
                'offer_id' => $offer->id,
                'amount' => 15.00,
                'wallet_amount' => 5.00,
                'card_amount' => 10.00,
            ]);

            $this->assertEquals(0.00, $buyer->fresh()->wallet);
        });
    }

    #[Test]
    public function a_user_can_pay_fully_with_wallet()
    {
        $this->markTestSkipped('Les tests de paiement sont désactivés pour éviter les transactions parasites.');
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 20.00]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 20.00]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 15.00,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                    ->visit(route('payment.create', $offer))
                    ->check('use_wallet')
                    ->assertSee('Total à payer : 0.00 €')
                    ->assertSee('Votre solde de portefeuille couvre la totalité de la commande.')
                    ->click('@submit-payment-button')
                    ->waitForText('Paiement effectué avec succès')
                    ->assertPathIs('/dashboard');

            $this->assertDatabaseHas('transactions', [
                'offer_id' => $offer->id,
                'amount' => 15.00,
                'wallet_amount' => 15.00,
                'card_amount' => 0.00,
            ]);

            $this->assertEquals(5.00, $buyer->fresh()->wallet);
        });
    }

    #[Test]
    public function a_user_can_pay_without_wallet()
    {
        $this->markTestSkipped('Les tests de paiement sont désactivés pour éviter les transactions parasites.');
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 5.00]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 20.00]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 15.00,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                    ->visit(route('payment.create', $offer))
                    ->uncheck('use_wallet')
                    ->assertSee('Total à payer : 15.00 €')
                    ->type('#card_number', '1234567812345678')
                    ->type('#expiry_date', '12/25')
                    ->type('#cvc', '123')
                    ->click('@submit-payment-button')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Paiement effectué avec succès');

            $this->assertDatabaseHas('transactions', [
                'offer_id' => $offer->id,
                'amount' => 15.00,
                'wallet_amount' => 0.00,
                'card_amount' => 15.00,
            ]);

            $this->assertEquals(5.00, $buyer->fresh()->wallet);
        });
    }
}
