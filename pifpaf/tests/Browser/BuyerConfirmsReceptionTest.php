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
    public function buyer_can_pay_and_then_confirm_reception(): void
    {
        $this->markTestSkipped('Les tests de paiement sont désactivés car ils dépendent de services externes non disponibles dans l\'environnement de test Dusk.');

        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'Article à Payer et Confirmer'
        ]);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'Article à Payer et Confirmer',
            'delivery_available' => true,
        ]);

        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'accepted',
            'delivery_method' => 'delivery',
        ]);

        Transaction::factory()->create([
            'offer_id' => $offer->id,
            'status' => 'initiated',
        ]);

        // 2. Act & Assert
        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                    ->visit('/dashboard')
                    ->assertSee('Article à Payer et Confirmer')
                    // Aller à la page de paiement
                    ->click('@pay-offer-' . $offer->id)
                    ->assertPathIs('/payment/' . $offer->id)
                    // Cliquer sur le bouton de paiement (simulé en test par le contrôleur)
                    ->click('@submit-payment-button')
                    // Vérifier la redirection et le succès
                    ->waitForText('Paiement effectué avec succès !')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Confirmer la réception')
                    // Confirmer la réception
                    ->press('Confirmer la réception')
                    ->acceptDialog()
                    ->waitForText('Réception confirmée. Le vendeur a été payé.')
                    ->assertSee('Réception confirmée. Le vendeur a été payé.')
                    ->assertDontSee('Confirmer la réception');
        });
    }
}
