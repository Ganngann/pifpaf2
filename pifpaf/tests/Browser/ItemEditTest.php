<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemEditTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanEditTheirOwnItem(): void
    {
        $user = User::factory()->create();
        $address = \App\Models\PickupAddress::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create(['user_id' => $user->id, 'pickup_address_id' => $address->id]);

        // Créer un fichier image réel pour le test
        $imageName = 'test-edit-image.jpg';
        $filePath = storage_path('app/public/temp_images/' . $imageName);
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        UploadedFile::fake()->image($imageName)->move(dirname($filePath), $imageName);

        $this->browse(function (Browser $browser) use ($user, $item, $filePath, $address) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->click('.edit-item-link') // S'assurer que le lien a cette classe
                    ->assertPathIs("/items/{$item->id}/edit")
                    ->assertInputValue('title', $item->title)
                    ->type('title', 'Nouveau Titre d\'Annonce')
                    ->type('description', 'Nouvelle description de l\'annonce.')
                    ->type('price', '150.75')
                    ->check('delivery_available')
                    ->check('pickup_available')
                    ->select('pickup_address_id', $address->id)
                    ->attach('images[]', $filePath)
                    ->press('Mettre à jour')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce mise à jour avec succès.')
                    ->assertSee('Nouveau Titre d\'Annonce');
        });
    }
}
