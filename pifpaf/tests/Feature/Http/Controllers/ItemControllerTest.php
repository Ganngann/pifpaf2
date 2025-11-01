<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_see_edit_item_page()
    {
        $item = Item::factory()->create();
        $response = $this->get(route('items.edit', $item));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_see_his_own_edit_item_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('items.edit', $item));

        $response->assertStatus(200);
        $response->assertViewIs('items.edit');
    }

    #[Test]
    public function authenticated_user_cannot_see_edit_page_for_other_users_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $itemOfOtherUser = Item::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('items.edit', $itemOfOtherUser));

        $response->assertStatus(403);
    }

    #[Test]
    public function authenticated_user_can_update_his_own_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Titre mis à jour',
            'description' => 'Description mise à jour',
            'category' => 'Autre',
            'price' => 123.45,
        ];

        $response = $this->actingAs($user)->put(route('items.update', $item), $updatedData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', array_merge(['id' => $item->id], $updatedData));
    }

    #[Test]
    public function authenticated_user_cannot_update_other_users_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $itemOfOtherUser = Item::factory()->create(['user_id' => $otherUser->id]);

        $updatedData = [
            'title' => 'Titre mis à jour',
            'description' => 'Description mise à jour',
            'price' => 123.45,
        ];

        $response = $this->actingAs($user)->put(route('items.update', $itemOfOtherUser), $updatedData);

        $response->assertStatus(403);
    }

    #[Test]
    public function authenticated_user_can_add_images_when_updating_item()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $this->assertCount(0, $item->images);

        $newImages = [
            UploadedFile::fake()->image('new_image1.jpg'),
        ];

        $updatedData = [
            'title' => $item->title,
            'description' => $item->description,
            'category' => $item->category,
            'price' => $item->price,
            'images' => $newImages,
        ];

        $response = $this->actingAs($user)->put(route('items.update', $item), $updatedData);

        $response->assertRedirect(route('dashboard'));
        $item->refresh();
        $this->assertCount(1, $item->images);
        Storage::disk('public')->assertExists($item->images->first()->path);
    }

    #[Test]
    public function authenticated_user_cannot_delete_other_users_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $itemOfOtherUser = Item::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('items.destroy', $itemOfOtherUser));

        $response->assertStatus(403);
        $this->assertDatabaseHas('items', ['id' => $itemOfOtherUser->id]);
    }
}
