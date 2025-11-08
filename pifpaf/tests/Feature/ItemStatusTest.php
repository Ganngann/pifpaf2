<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_unpublish_their_own_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('items.unpublish', $item));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'unpublished',
        ]);
    }

    #[Test]
    public function an_unpublished_item_is_not_visible_on_the_welcome_page()
    {
        $item = Item::factory()->create(['status' => 'unpublished']);

        $this->get(route('welcome'))
            ->assertDontSee($item->title);
    }

    #[Test]
    public function an_unpublished_item_is_still_visible_in_the_owners_dashboard()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'status' => 'unpublished',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee($item->title);
    }

    #[Test]
    public function another_user_cannot_unpublish_an_item()
    {
        $owner = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $owner->id, 'status' => 'available']);

        $notOwner = User::factory()->create();

        $this->actingAs($notOwner)
            ->post(route('items.unpublish', $item))
            ->assertForbidden();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'available',
        ]);
    }
}
