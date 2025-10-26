<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfilePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test to verify the user's public profile page.
     *
     * @return void
     */
    public function testUserProfilePage(): void
    {
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['user_id' => $user->id, 'title' => 'Mon premier objet à tester']);
        $item2 = Item::factory()->create(['user_id' => $user->id, 'title' => 'Mon deuxième objet à tester']);

        $this->browse(function (Browser $browser) use ($user, $item1, $item2) {
            $browser->visit(route('items.show', $item1))
                    ->assertSee($item1->title)
                    ->clickLink($user->name)
                    ->assertPathIs('/profile/' . $user->id)
                    ->assertSee($user->name)
                    ->assertSee($item1->title)
                    ->assertSee($item2->title);
        });
    }
}
