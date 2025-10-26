<?php

namespace Tests\Feature\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

#[Group('feature')]
#[Group('ai-creation')]
class AiItemCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_can_create_an_item_through_the_ai_flow(): void
    {
        // Créer un utilisateur et s'authentifier
        $user = User::factory()->create();
        $this->actingAs($user);

        // Utiliser un faux disque de stockage pour les tests
        Storage::fake('public');

        // 1. Simuler le téléversement de l'image pour analyse
        $file = UploadedFile::fake()->image('test_image.jpg');
        $response = $this->post(route('items.analyze-image'), [
            'image' => $file,
        ]);

        // 2. Vérifier la redirection et les données en session
        $response->assertStatus(302);
        $response->assertRedirect(route('items.create'));
        $response->assertSessionHas('ai_data');
        $response->assertSessionHas('image_path');

        $imagePath = session('image_path');
        Storage::disk('public')->assertExists($imagePath);

        // 3. Simuler la soumission du formulaire pré-rempli
        $aiData = session('ai_data');
        $storeResponse = $this->post(route('items.store'), [
            'title' => $aiData['title'],
            'description' => $aiData['description'],
            'category' => $aiData['category'],
            'price' => $aiData['price'],
            'image_path' => $imagePath, // Envoyer le chemin de l'image temporaire
        ]);

        // 4. Vérifier que l'annonce a été créée et l'image déplacée
        $storeResponse->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', [
            'title' => $aiData['title'],
            'user_id' => $user->id,
        ]);

        $item = \App\Models\Item::first();
        Storage::disk('public')->assertExists($item->image_path);
        Storage::disk('public')->assertMissing($imagePath); // Vérifier que l'ancienne image temporaire a été déplacée
        $this->assertStringContainsString('images/', $item->image_path);
    }
}
