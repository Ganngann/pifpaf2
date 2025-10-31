<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemImageUploadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste si un utilisateur peut téléverser plusieurs images en créant une annonce.
     *
     * @return void
     */
    public function test_user_can_upload_multiple_images_when_creating_item(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::fake('public');

        $images = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.png'),
        ];

        $itemData = [
            'title' => 'Mon Super Article',
            'description' => 'Une description détaillée.',
            'category' => 'Électronique',
            'price' => 100.50,
            'images' => $images,
        ];

        $response = $this->post(route('items.store'), $itemData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('items', 1);
        $this->assertDatabaseCount('item_images', 2);

        $item = \App\Models\Item::first();
        $this->assertCount(2, $item->images);

        foreach ($item->images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }
    }

    /**
     * Teste si le téléversement échoue si plus de 10 images sont envoyées.
     *
     * @return void
     */
    public function test_image_upload_fails_if_more_than_10_images(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $images = [];
        for ($i = 0; $i < 11; $i++) {
            $images[] = UploadedFile::fake()->image("photo{$i}.jpg");
        }

        $itemData = [
            'title' => 'Article avec trop d\'images',
            'description' => 'Description.',
            'category' => 'Sport',
            'price' => 50,
            'images' => $images,
        ];

        $response = $this->post(route('items.store'), $itemData);

        $response->assertSessionHasErrors('images');
        $this->assertDatabaseCount('items', 0);
    }

    /**
     * Teste si un utilisateur peut supprimer une image.
     *
     * @return void
     */
    public function test_user_can_delete_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $item = \App\Models\Item::factory()->create(['user_id' => $user->id]);

        // On crée une image manuellement pour ce test
        $imageFile = UploadedFile::fake()->image('image_to_delete.jpg');
        $path = $imageFile->store("item_images/{$item->id}", 'public');
        $image = $item->images()->create([
            'path' => $path,
            'is_primary' => true,
            'order' => 0
        ]);

        $this->actingAs($user);

        $response = $this->delete(route('item-images.destroy', $image));

        // On s'attend à une redirection vers la page d'édition avec un message de succès
        $response->assertRedirect(route('items.edit', $item));
        $response->assertSessionHas('success', 'Image supprimée avec succès.');

        $this->assertDatabaseMissing('item_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($image->path);
    }
}
