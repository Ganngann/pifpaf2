<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;

class BuyerConfirmsReceptionTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function buyer_can_see_and_click_the_confirm_reception_button(): void
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'title' => 'Article Test pour Confirmation']);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'paid',
        ]);
        $transaction = Transaction::factory()->create([
            'offer_id' => $offer->id,
            'status' => 'payment_received',
        ]);

        // 2. Act & Assert
        $this->browse(function (Browser $browser) use ($buyer) {
            $browser->loginAs($buyer)
                    ->visit('/dashboard')
                    ->assertSee('Article Test pour Confirmation')
                    ->assertSee('Confirmer la réception')
                    ->click('button[type="submit"]') // Clic sur le bouton de confirmation
                    ->acceptDialog() // Accepter la confirmation JS
                    ->waitForText('Réception confirmée. Le vendeur a été payé.')
                    ->assertSee('Réception confirmée. Le vendeur a été payé.')
                    ->assertDontSee('Confirmer la réception'); // Le bouton a disparu
        });
    }
}
