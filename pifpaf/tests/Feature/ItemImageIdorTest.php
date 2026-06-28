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

    public function test_user_cannot_reorder_other_users_images_via_mixed_array(): void
    {
        $attacker = User::factory()->create();
        $attackerItem = Item::factory()->create(['user_id' => $attacker->id]);
        $attackerImage = ItemImage::create([
            'item_id' => $attackerItem->id,
            'path' => 'attacker.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        $victim = User::factory()->create();
        $victimItem = Item::factory()->create(['user_id' => $victim->id]);
        $victimImage = ItemImage::create([
            'item_id' => $victimItem->id,
            'path' => 'victim.jpg',
            'is_primary' => false,
            'order' => 0,
        ]);

        // Attacker sends a mixed array containing their own image ID first (which passes initial auth),
        // but also includes the victim's image ID.
        $response = $this->actingAs($attacker)->postJson(route('item-images.reorder'), [
            'ids' => [$attackerImage->id, $victimImage->id],
        ]);

        // Assert that the victim's image was not reordered by the attacker
        $victimImage->refresh();
        $this->assertEquals(0, $victimImage->order);

        // Ensure the application handles it gracefully and succeeds for the authorized images
        $response->assertOk();
    }
}
