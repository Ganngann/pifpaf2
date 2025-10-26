<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class HeaderTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function testHeaderForGuests()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Connexion')
                    ->assertSee('Inscription')
                    ->assertDontSee('Tableau de bord');

            // Test logo link from homepage first
            $browser->click('@logo-link')
                    ->assertPathIs('/');

            // Then test navigation to login
            $browser->visit('/')
                    ->clickLink('Connexion')
                    ->assertPathIs('/login');
        });
    }

    #[Test]
    public function testHeaderForAuthenticatedUsers()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertDontSee('Connexion')
                    ->assertDontSee('Inscription')
                    ->assertSee('Tableau de bord')
                    ->click('@nav-user-dropdown')
                    ->waitFor('@nav-logout') // Wait for the logout link to be visible
                    ->assertSee('Déconnexion');

            // Test logo link
            $browser->visit('/dashboard')
                     ->click('@logo-link')
                     ->assertPathIs('/');
        });
    }
}
