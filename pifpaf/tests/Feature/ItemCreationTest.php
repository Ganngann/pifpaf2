<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste la création d'une annonce par un utilisateur authentifié.
     */
    public function test_authenticated_user_can_create_item(): void
    {
        // Crée un utilisateur et s'authentifie en tant que cet utilisateur
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simule le téléversement d'un fichier image
        Storage::fake('public');
        $file = UploadedFile::fake()->image('item.jpg');

        // Données du formulaire
        $itemData = [
            'title' => 'Mon Super Article',
            'description' => 'Ceci est une description de mon article.',
            'price' => 99.99,
            'image' => $file,
        ];

        // Envoie la requête POST pour créer l'annonce
        $response = $this->post(route('items.store'), $itemData);

        // Vérifie la redirection et le message de succès
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Annonce créée avec succès.');

        // Vérifie que l'article a été créé dans la base de données
        $this->assertDatabaseHas('items', [
            'title' => 'Mon Super Article',
            'user_id' => $user->id,
        ]);

        // Vérifie que l'image a été stockée
        Storage::disk('public')->assertExists('images/' . $file->hashName());
    }
}
