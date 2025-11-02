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
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'title' => 'Article à Payer et Confirmer'
        ]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'accepted', // L'offre est juste acceptée, pas encore payée
        ]);

        // 2. Act & Assert
        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                    ->visit('/dashboard')
                    ->assertSee('Article à Payer et Confirmer')
                    // Étape 1: Payer
                    ->click('@pay-offer-' . $offer->id)
                    ->assertPathIs('/payment/' . $offer->id)
                    // Remplir les champs du formulaire de paiement de manière robuste
                    ->value('#card_number', '1234567891011121')
                    ->value('#expiry_date', '12/25')
                    ->value('#cvc', '123')
                    ->click('@submit-payment-button')
                    // Étape 2: Vérifier l'affichage du bouton de confirmation après paiement
                    ->waitForText('Paiement effectué avec succès !')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Confirmer la réception') // Le bouton doit être visible !
                    // Étape 3: Confirmer la réception
                    ->click('button[type="submit"]')
                    ->acceptDialog()
                    ->waitForText('Réception confirmée. Le vendeur a été payé.')
                    ->assertSee('Réception confirmée. Le vendeur a été payé.')
                    ->assertDontSee('Confirmer la réception');
        });
    }
}
