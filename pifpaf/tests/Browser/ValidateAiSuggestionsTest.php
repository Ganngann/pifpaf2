<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class ValidateAiSuggestionsTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function user_can_validate_and_edit_ai_suggestions_to_create_an_item(): void
    {
        $this->markTestSkipped('Skipping AI test due to external API dependency issues.');
        // 1. Préparation
        $user = User::factory()->create();

        Storage::fake('public');
        $image = UploadedFile::fake()->image('ai-test-image.jpg');
        $imagePath = $image->store('test-images', 'public');
        $absoluteImagePath = Storage::disk('public')->path($imagePath);

        $this->browse(function (Browser $browser) use ($user, $absoluteImagePath) {
            // 2. Actions
            $browser->loginAs($user)
                    ->visit(route('items.create-with-ai'))
                    ->assertSee('Vendre un article avec l\'IA') // Correction du texte
                    ->attach('image', $absoluteImagePath)
                    ->press('Analyser l\'image')
                    ->waitForRoute('items.create')
                    ->assertPathIs('/items/create');

            // 3. Assertions sur le formulaire pré-rempli
            $browser->assertInputValue('title', 'Objet Détecté par l\'IA')
                    ->assertValue('textarea[name="description"]', 'Ceci est une description générée automatiquement par notre IA.')
                    ->assertSelected('category', 'Électronique')
                    ->assertInputValue('price', '99.99');

            // 4. Modifier une valeur et soumettre
            $newTitle = 'Mon Superbe Vélo Rouge';
            $browser->type('title', $newTitle)
                    ->press('Mettre en vente')
                    ->waitForRoute('dashboard')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce créée avec succès.');

            // 5. Assertion finale en base de données
            $this->assertDatabaseHas('items', [
                'user_id' => $user->id,
                'title' => $newTitle,
                'description' => 'Ceci est une description générée automatiquement par notre IA.',
                'category' => 'Électronique',
                'price' => 99.99,
            ]);
        });
    }
}
