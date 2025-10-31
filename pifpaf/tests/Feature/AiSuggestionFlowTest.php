<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\GoogleAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AiSuggestionFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function image_analysis_redirects_with_ai_data_in_session_and_prefills_form(): void
    {
        // 1. Préparation
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('test_image.jpg');

        $expectedAiData = [
            'title' => 'Objet Détecté par l\'IA',
            'description' => 'Ceci est une description générée automatiquement par notre IA.',
            'category' => 'Électronique',
            'price' => 99.99,
        ];

        // Simuler le service d'IA
        $this->mock(GoogleAiService::class, function ($mock) use ($expectedAiData) {
            $mock->shouldReceive('analyzeImage')->andReturn($expectedAiData);
        });

        // 2. Action
        $response = $this->post(route('items.analyze-image'), [
            'image' => $file,
        ]);

        // 3. Assertions de redirection et de session
        $response->assertRedirect(route('items.create'));
        $response->assertSessionHas('ai_data', $expectedAiData);
        $response->assertSessionHas('image_path');

        // Suivre la redirection
        $followResponse = $this->get(route('items.create'));
        $followResponse->assertStatus(200);

        // 4. Assertions sur le contenu HTML
        $content = $followResponse->getContent();

        $expectedTitle = e('Valider les informations de l\'annonce');
        $this->assertStringContainsString($expectedTitle, $content);

        $escapedFieldTitle = e($expectedAiData['title']);
        $this->assertStringContainsString('value="' . $escapedFieldTitle . '"', $content);

        $escapedDescription = e($expectedAiData['description']);
        $this->assertStringContainsString('>' . $escapedDescription . '</textarea>', $content);

        $this->assertStringContainsString('value="' . $expectedAiData['price'] . '"', $content);

        // Utiliser une expression régulière pour vérifier la sélection de la catégorie
        // Cela recherche une balise <option> pour "Électronique" qui contient l'attribut "selected"
        $this->assertMatchesRegularExpression('/<option value="Électronique"[^>]*selected[^>]*>/', $content);

        $this->assertStringContainsString('Mettre en vente', $content);
    }
}
