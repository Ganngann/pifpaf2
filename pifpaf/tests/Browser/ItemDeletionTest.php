<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Http\UploadedFile;

class ItemDeletionTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A user can delete their own item.
     *
     * @return void
     */
    public function testUserCanDeleteItem()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->assertSee($item->title)
                    // The delete button does not exist yet
                    ->press('Supprimer')
                    ->acceptDialog()
                    ->assertPathIs('/dashboard')
                    ->assertDontSee($item->title)
                    ->assertSee('Annonce supprimée avec succès.');
        });

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}
