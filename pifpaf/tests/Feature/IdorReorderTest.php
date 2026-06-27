<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdorReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_reorder_mixed_images(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $item1 = Item::factory()->create(['user_id' => $user1->id]);
        $item2 = Item::factory()->create(['user_id' => $user2->id]);

        $image1 = ItemImage::create([
            'item_id' => $item1->id,
            'path' => 'fake1.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $image2 = ItemImage::create([
            'item_id' => $item2->id,
            'path' => 'fake2.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $response = $this->actingAs($user1)->postJson(route('item-images.reorder'), [
            'ids' => [$image1->id, $image2->id],
        ]);

        if ($response->status() === 200) {
           $this->assertDatabaseMissing('item_images', [
               'id' => $image2->id,
               'order' => 1,
           ]);
        } else {
           $response->assertForbidden();
        }
    }
}
