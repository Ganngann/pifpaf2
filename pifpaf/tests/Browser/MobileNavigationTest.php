<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MobileNavigationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_messaging_link_is_visible_and_functional_in_mobile_menu(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->resize(375, 812) // Simuler une taille d'iPhone X
                    ->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@hamburger-button') // Clic sur le bouton du menu hamburger via son attribut dusk
                    ->waitForText('Messagerie') // Attendre que le menu s'ouvre et que le lien soit visible
                    ->clickLink('Messagerie')
                    ->assertPathIs('/conversations');
        });
    }
}
