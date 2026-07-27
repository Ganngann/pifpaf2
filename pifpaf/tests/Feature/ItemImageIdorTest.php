<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemImageIdorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_reorder_other_users_images()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $item1 = Item::factory()->create(['user_id' => $user->id]);
        $item2 = Item::factory()->create(['user_id' => $otherUser->id]);

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
            'order' => 1,
        ]);

        // When we reorder image1 and image2
        // Since image1 is first, the controller will authorize against item1 (which we own)
        // Then it will loop over both IDs
        // With the fix, it will only update image1 because image2 has a different item_id
        $response = $this->actingAs($user)->postJson(route('item-images.reorder'), [
            'ids' => [$image1->id, $image2->id],
        ]);

        $response->assertOk();

        // Refresh model from DB to see if the order changed
        $image2->refresh();
        $this->assertEquals(1, $image2->order, 'IDOR vulnerability mitigated! Image order did not change.');

        $image1->refresh();
        $this->assertEquals(0, $image1->order);
    }
}
