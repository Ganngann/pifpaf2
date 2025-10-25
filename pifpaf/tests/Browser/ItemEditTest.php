<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemEditTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanEditTheirOwnItem(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->clickLink('Modifier')
                    ->assertPathIs("/items/{$item->id}/edit")
                    ->assertInputValue('title', $item->title)
                    ->type('title', 'Nouveau Titre d\'Annonce')
                    ->type('description', 'Nouvelle description de l\'annonce.')
                    ->type('price', '150.75')
                    ->attach('image', storage_path('app/public/images/test.jpg'))
                    ->press('Mettre à jour')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce mise à jour avec succès.')
                    ->assertSee('Nouveau Titre d\'Annonce');
        });
    }
}
