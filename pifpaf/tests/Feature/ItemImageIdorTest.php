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

    public function test_user_cannot_reorder_other_users_images_with_mixed_ids(): void
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

        $response = $this->actingAs($attacker)->postJson(route('item-images.reorder'), [
            'ids' => [$attackerImage->id, $victimImage->id],
        ]);

        $response->assertForbidden();
    }
}
