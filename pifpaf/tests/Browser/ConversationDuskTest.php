<?php

namespace Tests\Browser;

use App\Models\Address;
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
        $address = Address::factory()->create(['user_id' => $seller->id]);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'address_id' => $address->id,
            'pickup_available' => true,
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $item) {
            $browser->loginAs($buyer)
                    ->visit(route('items.show', $item))
                    ->click('@contact-seller-button')
                    ->waitForText($item->title)
                    ->assertPathIs('/conversations/*')
                    ->type('content', 'Bonjour, cet article est-il toujours disponible ?')
                    ->press('Envoyer')
                    ->waitForText('Bonjour, cet article est-il toujours disponible ?')
                    ->assertSee('Bonjour, cet article est-il toujours disponible ?');
        });
    }
}
