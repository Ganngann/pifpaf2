<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemImageControllerIdorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_reorder_other_users_images_via_mixed_array(): void
    {
        $attacker = User::factory()->create();
        $attackerItem = Item::factory()->create(['user_id' => $attacker->id]);
        $attackerImage = ItemImage::create([
            'item_id' => $attackerItem->id,
            'path' => 'fake1.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $victim = User::factory()->create();
        $victimItem = Item::factory()->create(['user_id' => $victim->id]);
        $victimImage = ItemImage::create([
            'item_id' => $victimItem->id,
            'path' => 'fake2.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $response = $this->actingAs($attacker)->postJson(route('item-images.reorder'), [
            'ids' => [$attackerImage->id, $victimImage->id],
        ]);

        // The reorder might succeed or fail, but the victim's image should not have its order changed
        // Or better, it should be forbidden or abort
        $this->assertDatabaseHas('item_images', [
            'id' => $victimImage->id,
            'order' => 0,
        ]);
    }
}
