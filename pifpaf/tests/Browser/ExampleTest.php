<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testHomepageScreenshot(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitForText("Let's get started")
                    ->assertSee("Let's get started")
                    ->screenshot('homepage');
        });
    }
}
