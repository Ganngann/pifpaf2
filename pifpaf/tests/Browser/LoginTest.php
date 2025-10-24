<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test for user login.
     */
    public function test_a_user_can_login_via_the_form(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->screenshot('login-page')
                    ->type('email', $user->email)
                    ->type('password', 'password') // Default factory password
                    ->press('Log in')
                    ->assertPathIs('/dashboard')
                    ->assertSee("Vous êtes connecté !");
        });
    }

    public function test_a_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'wrong-password')
                ->press('Log in')
                ->waitForText('These credentials do not match our records.')
                ->screenshot('login-error')
                ->assertPathIs('/login');
        });
    }

    public function test_a_logged_in_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->click('.relative button') // Ouvre le menu déroulant
                ->clickLink('Déconnexion')
                ->assertPathIs('/')
                ->assertGuest();
        });
    }
}
