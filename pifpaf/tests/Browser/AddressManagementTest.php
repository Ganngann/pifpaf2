<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AddressManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeAddressesLinkInNavigation()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@nav-user-dropdown')
                    ->waitFor('@nav-addresses-link')
                    ->assertSee('Mes Adresses');
        });
    }

    public function testUserCanNavigateToAddressesPage()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('@nav-user-dropdown')
                    ->waitFor('@nav-addresses-link')
                    ->click('@nav-addresses-link')
                    ->assertPathIs('/profile/addresses')
                    ->assertSee('Toutes mes adresses');
        });
    }

    public function testUserCanAddANewAddressForBothPickupAndDelivery()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/profile/addresses')
                ->clickLink('Ajouter une adresse')
                ->assertPathIs('/profile/addresses/create')
                ->type('name', 'Maison & Bureau')
                ->type('street', '123 rue de Paris')
                ->type('city', 'Paris')
                ->type('postal_code', '75001')
                ->type('country', 'France')
                ->check('is_for_pickup')
                ->check('is_for_delivery')
                ->press('button[type="submit"]')
                ->waitForText('Adresse ajoutée avec succès.')
                ->assertPathIs('/profile/addresses')
                ->assertSee('Maison & Bureau')
                ->assertSee('Retrait')
                ->assertSee('Livraison');
        });
    }
}
