<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test for user login.
     */
    #[Test]
    public function a_user_can_login_via_the_form(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password') // Default factory password
                    ->press('Log in')
                    ->assertPathIs('/dashboard')
                    ->assertSee("Tableau de bord");
        });
    }

    #[Test]
    public function a_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'wrong-password')
                ->press('Log in')
                ->pause(500) // Add a small pause
                ->waitForText('Ces identifiants ne correspondent pas à nos enregistrements.')
                ->assertSee('Ces identifiants ne correspondent pas à nos enregistrements.')
                ->assertPathIs('/login');
        });
    }

    #[Test]
    public function a_logged_in_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->click('@nav-user-dropdown')
                ->waitFor('@nav-logout')
                // Directly execute the form submission with JavaScript for reliability
                ->script('document.querySelector(\'[dusk="logout-form"]\').submit()');

            $browser->assertPathIs('/')
                    ->assertGuest();
        });
    }
}
