<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ConversationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_a_user_can_start_a_conversation_and_send_a_message(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $this->browse(function (Browser $browser) use ($buyer, $item) {
            $browser->loginAs($buyer)
                    ->visit(route('items.show', $item))
                    ->waitForText('Contacter le vendeur')
                    ->clickLink('Contacter le vendeur')
                    ->assertPathIs('/conversations/create')
                    ->assertSee($item->title)
                    ->type('content', 'Bonjour, cet article est-il toujours disponible ?')
                    ->press('Envoyer')
                    ->assertSee('Bonjour, cet article est-il toujours disponible ?');
        });
    }
}
