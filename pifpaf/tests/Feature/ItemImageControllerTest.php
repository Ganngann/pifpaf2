<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemImageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_item_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $imageFile = UploadedFile::fake()->image('test.jpg');
        $path = $imageFile->store("item_images/{$item->id}", 'public');

        $image = ItemImage::create([
            'item_id' => $item->id,
            'path' => $path,
            'is_primary' => false,
            'order' => 0,
        ]);

        $response = $this->actingAs($user)->delete(route('item-images.destroy', $image));

        $response->assertRedirect(route('items.edit', $item));
        $this->assertDatabaseMissing('item_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_can_set_primary_image(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $image1 = ItemImage::create([
            'item_id' => $item->id,
            'path' => 'fake1.jpg',
            'is_primary' => true,
            'order' => 0,
        ]);

        $image2 = ItemImage::create([
            'item_id' => $item->id,
            'path' => 'fake2.jpg',
            'is_primary' => false,
            'order' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('item-images.set-primary', $image2));

        $response->assertRedirect(route('items.edit', $item));
        $this->assertDatabaseHas('item_images', [
            'id' => $image1->id,
            'is_primary' => false,
        ]);
        $this->assertDatabaseHas('item_images', [
            'id' => $image2->id,
            'is_primary' => true,
        ]);
    }

    public function test_user_can_reorder_images(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $image1 = ItemImage::create([
            'item_id' => $item->id,
            'path' => 'fake1.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $image2 = ItemImage::create([
            'item_id' => $item->id,
            'path' => 'fake2.jpg',
            'is_primary' => false,
            'order' => 1,
        ]);

        $response = $this->actingAs($user)->postJson(route('item-images.reorder'), [
            'ids' => [$image2->id, $image1->id],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('item_images', [
            'id' => $image2->id,
            'order' => 0,
        ]);
        $this->assertDatabaseHas('item_images', [
            'id' => $image1->id,
            'order' => 1,
        ]);
    }

    public function test_user_cannot_modify_other_users_images(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $otherUser->id]);

        $image = ItemImage::create([
            'item_id' => $item->id,
            'path' => 'fake.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $responseDelete = $this->actingAs($user)->delete(route('item-images.destroy', $image));
        $responseDelete->assertForbidden();

        $responseSetPrimary = $this->actingAs($user)->post(route('item-images.set-primary', $image));
        $responseSetPrimary->assertForbidden();

        $responseReorder = $this->actingAs($user)->postJson(route('item-images.reorder'), [
            'ids' => [$image->id],
        ]);
        $responseReorder->assertForbidden();
    }

    public function test_user_cannot_reorder_other_users_images_via_idor(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $image1 = ItemImage::create([
            'item_id' => $item->id,
            'path' => 'fake1.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $otherUser = User::factory()->create();
        $otherItem = Item::factory()->create(['user_id' => $otherUser->id]);

        $otherImage = ItemImage::create([
            'item_id' => $otherItem->id,
            'path' => 'fake2.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        // Attempt to reorder by passing own image first (to pass authorize check),
        // then the other user's image.
        $response = $this->actingAs($user)->postJson(route('item-images.reorder'), [
            'ids' => [$image1->id, $otherImage->id],
        ]);

        $response->assertOk();

        // Own image is updated.
        $this->assertDatabaseHas('item_images', [
            'id' => $image1->id,
            'order' => 0,
        ]);

        // Other user's image remains unchanged (order is still 0, not 1).
        $this->assertDatabaseHas('item_images', [
            'id' => $otherImage->id,
            'order' => 0,
        ]);
    }
}
