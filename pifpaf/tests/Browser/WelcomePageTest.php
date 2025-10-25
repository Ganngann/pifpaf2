<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test que la page d'accueil affiche les derniers articles.
     *
     * @return void
     */
    public function test_welcome_page_displays_latest_items()
    {
        $user = User::factory()->create();
        $items = Item::factory()->count(5)->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($items) {
            $browser->visit('/')
                    ->assertSee('Derniers articles');

            foreach ($items as $item) {
                $browser->assertSee($item->title)
                        ->assertSee($item->price);
            }
        });
    }

    /**
     * Test que cliquer sur un article mène à sa page de détail.
     *
     * @return void
     */
    public function test_clicking_item_leads_to_item_details_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($item) {
            $browser->visit('/')
                    ->clickLink($item->title)
                    ->assertPathIs('/items/' . $item->id)
                    ->assertSee($item->title)
                    ->assertSee($item->description)
                    ->assertSee($item->price);
        });
    }
}
