<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class UserRegistrationBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test for user registration.
     *
     * @return void
     */
    public function testUserRegistration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->screenshot('registration-page') // Screenshot of the registration page
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('Register')
                    ->assertPathIs('/dashboard')
                ->assertSee("You're logged in!")
                    ->screenshot('dashboard');
        });
    }
}
