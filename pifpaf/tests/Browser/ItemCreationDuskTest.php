<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemCreationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Teste la création d'une annonce via le formulaire du navigateur.
     */
    public function testUserCanCreateItemThroughBrowser(): void
    {
        $user = User::factory()->create();

        // Crée un fichier image factice mais valide
        $fakeImage = UploadedFile::fake()->image('test-image.jpg');

        $this->browse(function (Browser $browser) use ($user, $fakeImage) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->clickLink('Vendre un article')
                    ->assertPathIs('/items/create')
                    ->type('title', 'Superbe Lampe Vintage')
                    ->type('description', 'Une lampe unique pour décorer votre intérieur.')
                    ->type('price', '75.50')
                    ->attach('image', $fakeImage)
                    ->press('Mettre en vente')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce créée avec succès.');
        });
    }
}
