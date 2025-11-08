<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

#[Group('browser')]
#[Group('ai-creation')]
class AiItemCreationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function a_user_can_create_an_item_using_the_ai_flow(): void
    {
        $this->markTestSkipped('Skipping AI test due to external API dependency issues.');
        $user = User::factory()->create();

        // Créer un fichier image réel pour le test
        $imageName = 'test_dusk_image.jpg';
        $filePath = storage_path('framework/testing/' . $imageName);
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        UploadedFile::fake()->image($imageName)->move(dirname($filePath), $imageName);

        $this->browse(function (Browser $browser) use ($user, $filePath) {
            $browser->loginAs($user)
                    ->visit('/items/create-with-ai')
                    ->screenshot('debug_ai_creation_page') // Ajout de la capture d'écran
                    ->waitForText('Vendre un article avec l\'IA')
                    ->assertSee('Vendre un article avec l\'IA')

                    ->attach('image', $filePath)
                    ->press('Analyser l\'image')

                    ->waitForText('Valider les informations de l\'annonce')
                    ->assertSee('Valider les informations de l\'annonce')
                    ->assertInputValue('title', 'Objet Détecté par l\'IA')
                    ->assertInputValue('description', 'Ceci est une description générée automatiquement par notre IA.')
                    ->assertSelected('category', 'Électronique')
                    ->assertInputValue('price', '99.99')

                    ->press('Mettre en vente')

                    ->waitForText('Annonce créée avec succès.')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce créée avec succès.')
                    ->assertSee('Objet Détecté par l\'IA');
        });

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
