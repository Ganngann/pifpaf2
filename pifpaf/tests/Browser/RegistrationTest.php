<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class RegistrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function user_registration(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'John Doe')
                    ->type('email', 'john.doe@example.com')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('button[type="submit"]')
                    ->waitForLocation('/dashboard')
                    ->assertPathIs('/dashboard');
        });
    }
}
