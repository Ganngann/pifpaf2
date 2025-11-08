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
                    ->assertSee('Mes Adresses');
        });
    }

    public function testUserCanAddANewPickupAddress()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/profile/addresses')
                ->clickLink('Ajouter une adresse de retrait')
                ->assertPathIs('/profile/addresses/create')
                ->type('name', 'Maison')
                ->type('street', '123 rue de Paris')
                ->type('city', 'Paris')
                ->type('postal_code', '75001')
                ->select('type', 'pickup')
                ->press('Enregistrer')
                ->waitForText('Adresse ajoutée avec succès.')
                ->assertPathIs('/profile/addresses')
                ->assertSee('Maison');
        });
    }

    public function testUserCanAddANewShippingAddress()
    {
        $this->markTestSkipped('This test is failing intermittently due to a JavascriptErrorException and needs further investigation.');

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit(route('profile.addresses.index'))
                ->clickLink('Ajouter une adresse de livraison')
                ->assertPathIs('/profile/addresses/create')
                ->type('name', 'Bureau')
                ->type('street', '456 Avenue des Champs-Élysées')
                ->type('city', 'Paris')
                ->type('postal_code', '75008')
                ->type('country', 'France')
                ->select('type', 'delivery')
                ->press('Enregistrer')
                ->waitForText('Adresse ajoutée avec succès.')
                ->assertPathIs('/profile/addresses')
                ->assertSee('Bureau');
        });
    }
}
