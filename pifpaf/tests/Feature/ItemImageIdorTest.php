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

    public function test_user_cannot_reorder_mixed_images()
    {
        $attacker = User::factory()->create();
        $victim = User::factory()->create();

        $attackerItem = Item::factory()->create(['user_id' => $attacker->id]);
        $victimItem = Item::factory()->create(['user_id' => $victim->id]);

        $attackerImage = ItemImage::create(['item_id' => $attackerItem->id, 'path' => 'a.jpg', 'is_primary' => false, 'order' => 0]);
        $victimImage = ItemImage::create(['item_id' => $victimItem->id, 'path' => 'v.jpg', 'is_primary' => false, 'order' => 1]);

        $response = $this->actingAs($attacker)->postJson(route('item-images.reorder'), [
            'ids' => [$attackerImage->id, $victimImage->id],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('item_images', [
            'id' => $victimImage->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('item_images', [
            'id' => $attackerImage->id,
            'order' => 0,
        ]);
    }
}
