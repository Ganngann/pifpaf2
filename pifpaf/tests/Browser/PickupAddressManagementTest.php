<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class PickupAddressManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test to see the navigation link.
     *
     * @return void
     */
    public function testUserCanSeeAddressesLinkInNavigation()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->assertSee('Mes Adresses');
        });
    }

    public function testUserCanNavigateToAddressesPage()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->clickLink('Mes Adresses')
                    ->assertPathIs('/profile/addresses')
                    ->assertSee('Mes Adresses de Retrait');
        });
    }

    public function testUserCanAddANewPickupAddress()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/profile/addresses')
                ->clickLink('Ajouter une nouvelle adresse')
                ->assertPathIs('/profile/addresses/create')
                ->type('name', 'Maison')
                ->type('street', '123 rue de Paris')
                ->type('city', 'Paris')
                ->type('postal_code', '75001')
                ->press('Enregistrer')
                ->assertPathIs('/profile/addresses')
                ->assertSee('Adresse ajoutée avec succès.')
                ->assertSee('Maison');
        });
    }

}
