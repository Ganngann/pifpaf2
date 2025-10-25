<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_see_edit_item_page()
    {
        $item = Item::factory()->create();
        $response = $this->get(route('items.edit', $item));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_see_his_own_edit_item_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('items.edit', $item));

        $response->assertStatus(200);
        $response->assertViewIs('items.edit');
    }

    public function test_authenticated_user_cannot_see_edit_page_for_other_users_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $itemOfOtherUser = Item::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('items.edit', $itemOfOtherUser));

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_update_his_own_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Titre mis à jour',
            'description' => 'Description mise à jour',
            'price' => 123.45,
        ];

        $response = $this->actingAs($user)->put(route('items.update', $item), $updatedData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', array_merge(['id' => $item->id], $updatedData));
    }

    public function test_authenticated_user_cannot_update_other_users_item()
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

    public function test_authenticated_user_can_update_item_with_new_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $oldImage = UploadedFile::fake()->image('old_image.jpg');
        $oldImagePath = $oldImage->store('images', 'public');
        $item = Item::factory()->create(['user_id' => $user->id, 'image_path' => $oldImagePath]);

        $newImage = UploadedFile::fake()->image('new_image.jpg');

        $updatedData = [
            'title' => 'Titre mis à jour',
            'description' => 'Description mise à jour',
            'price' => 123.45,
            'image' => $newImage,
        ];

        $response = $this->actingAs($user)->put(route('items.update', $item), $updatedData);

        $response->assertRedirect(route('dashboard'));

        $item->refresh();

        $this->assertNotEquals($oldImagePath, $item->image_path);
        Storage::disk('public')->assertExists($item->image_path);
        Storage::disk('public')->assertMissing($oldImagePath);
    }

    public function test_authenticated_user_cannot_delete_other_users_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $itemOfOtherUser = Item::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('items.destroy', $itemOfOtherUser));

        $response->assertStatus(403);
        $this->assertDatabaseHas('items', ['id' => $itemOfOtherUser->id]);
    }
}
