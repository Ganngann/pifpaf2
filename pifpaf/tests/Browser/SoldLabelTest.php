<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Enums\ItemStatus;

class SoldLabelTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testSoldLabelIsVisibleOnSoldItems()
    {
        $user = User::factory()->create();
        $soldItem = Item::factory()->create(['user_id' => $user->id, 'status' => ItemStatus::SOLD]);
        $availableItem = Item::factory()->create(['user_id' => $user->id, 'status' => ItemStatus::AVAILABLE]);

        $buyer = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $soldItem, $availableItem, $buyer) {
            $browser->loginAs($buyer)
                    ->visit(route('profile.show', $user))
                    ->assertSee($soldItem->title)
                    ->assertSee($availableItem->title)
                    ->within("#item-{$soldItem->id}", function ($browser) {
                        $browser->assertSee('VENDU');
                    })
                    ->within("#item-{$availableItem->id}", function ($browser) {
                        $browser->assertDontSee('VENDU');
                    });
        });
    }
}
