<?php

namespace Tests\Feature\Item;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_access_the_create_item_form()
    {
        $response = $this->get(route('items.create'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_can_access_the_create_item_form()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('items.create'));

        $response->assertStatus(200);
        $response->assertViewIs('items.create');
    }

    /** @test */
    public function it_creates_an_item_successfully_with_valid_data()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $imageData = UploadedFile::fake()->image('test-image.jpg');

        $itemData = [
            'title' => 'Mon Super Article',
            'description' => 'Ceci est une description de l\'article.',
            'category' => 'Électronique',
            'price' => 99.99,
            'image' => $imageData,
        ];

        $response = $this->post(route('items.store'), $itemData);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Annonce créée avec succès.');

        $this->assertDatabaseHas('items', [
            'title' => 'Mon Super Article',
            'user_id' => $user->id,
        ]);

        $imagePath = 'images/' . $imageData->hashName();
        Storage::disk('public')->assertExists($imagePath);
    }

    /** @test */
    public function it_fails_validation_when_required_fields_are_missing()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test sans le titre
        $response = $this->post(route('items.store'), ['description' => 'test', 'price' => 10, 'image' => UploadedFile::fake()->image('test.jpg')]);
        $response->assertSessionHasErrors('title');

        // Test sans la description
        $response = $this->post(route('items.store'), ['title' => 'test', 'price' => 10, 'image' => UploadedFile::fake()->image('test.jpg')]);
        $response->assertSessionHasErrors('description');

        // Test sans le prix
        $response = $this->post(route('items.store'), ['title' => 'test', 'description' => 'test', 'image' => UploadedFile::fake()->image('test.jpg')]);
        $response->assertSessionHasErrors('price');

        // Test sans l'image
        $response = $this->post(route('items.store'), ['title' => 'test', 'description' => 'test', 'price' => 10]);
        $response->assertSessionHasErrors('image');

        // Test sans la catégorie
        $response = $this->post(route('items.store'), ['title' => 'test', 'description' => 'test', 'price' => 10, 'image' => UploadedFile::fake()->image('test.jpg')]);
        $response->assertSessionHasErrors('category');
    }
}
