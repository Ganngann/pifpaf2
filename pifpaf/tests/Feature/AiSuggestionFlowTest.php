<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Services\GoogleAiService;
use App\Models\User;

class AiSuggestionFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function image_analysis_redirects_with_ai_data_in_session_and_prefills_form()
    {
        // 1. Préparation
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('item.jpg');

        $expectedAiData = [
            'title' => 'T-shirt Vintage',
            'description' => 'Un t-shirt en bon état.',
            'category' => 'Vêtements',
            'price' => 15.0,
        ];

        // Mock du service AI
        $mock = Mockery::mock(GoogleAiService::class);
        $mock->shouldReceive('analyzeImage')->once()->andReturn([$expectedAiData]); // Retourne un tableau avec un seul objet
        $this->app->instance(GoogleAiService::class, $mock);

        // 2. Action
        $response = $this->post(route('items.analyze-image'), [
            'image' => $file,
        ]);

        // 3. Assertions
        $response->assertRedirect(route('items.create'));
        $response->assertSessionHas('ai_data', $expectedAiData);
        $response->assertSessionHas('image_path');

        // Suivre la redirection
        $followUpResponse = $this->get($response->headers->get('Location'));
        $followUpResponse->assertOk();
        $followUpResponse->assertSee($expectedAiData['title']);
        $followUpResponse->assertSee($expectedAiData['description']);
    }
}
