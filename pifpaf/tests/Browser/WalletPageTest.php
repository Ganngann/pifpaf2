<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class WalletPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Teste l'affichage de la page du portefeuille.
     *
     * @return void
     */
    public function testWalletPageDisplaysCorrectly(): void
    {
        $user = User::factory()->create(['wallet' => 123.45]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/wallet')
                    ->assertSee('Mon Portefeuille')
                    ->assertSee('Solde actuel')
                    ->assertSee('123,45 €');
        });
    }

    /**
     * Teste que le lien de navigation vers le portefeuille fonctionne.
     *
     * @return void
     */
    public function testNavigationLinkToWalletWorks(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@nav-user-dropdown')
                    ->waitForText('Mon Portefeuille')
                    ->clickLink('Mon Portefeuille')
                    ->assertPathIs('/wallet')
                    ->assertSee('Mon Portefeuille');
        });
    }
}
