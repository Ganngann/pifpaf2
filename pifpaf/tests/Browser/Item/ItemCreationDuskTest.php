<?php

namespace Tests\Browser\Item;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemCreationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test for item creation.
     *
     * @return void
     */
    public function testItemCreationFlow()
    {
        $user = User::factory()->create();

        // Créer un fichier image réel pour le test
        $imageName = 'test-image.jpg';
        $filePath = storage_path('app/public/temp_images/' . $imageName);
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        UploadedFile::fake()->image($imageName)->move(dirname($filePath), $imageName);

        $this->browse(function (Browser $browser) use ($user, $filePath) {
            $browser->loginAs($user)
                    ->visit(route('items.create'))
                    ->assertSee('Créer une nouvelle annonce');

            // --- Capture d'écran Desktop ---
            $browser->screenshot('item_creation_desktop');

            // --- Capture d'écran Mobile ---
            $browser->resize(375, 812) // iPhone X dimensions
                    ->screenshot('item_creation_mobile');

            // Remplissage du formulaire
            $browser->type('title', 'Superbe Vase Ancien')
                    ->type('description', 'Un vase rare du 18ème siècle, en parfait état.')
                    ->select('category', 'Maison') // Ajout de la catégorie
                    ->type('price', '150.75')
                    ->attach('image', $filePath) // Utiliser le fichier image factice
                    ->press('Créer l\'annonce');

            // Vérification de la redirection et du message
            $browser->assertPathIs('/dashboard')
                    ->assertSee('Annonce créée avec succès.');
        });

        // Nettoyer le fichier image après le test
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
