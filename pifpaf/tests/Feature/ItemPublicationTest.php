<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Enums\ItemStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemPublicationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_publish_their_own_unpublished_item(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id, 'status' => ItemStatus::UNPUBLISHED]);

        $response = $this->actingAs($user)->post(route('items.publish', $item));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => ItemStatus::AVAILABLE->value,
        ]);
    }

    #[Test]
    public function a_user_cannot_publish_another_users_item(): void
    {
        $owner = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $owner->id, 'status' => ItemStatus::UNPUBLISHED]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->post(route('items.publish', $item));

        $response->assertStatus(403);
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => ItemStatus::UNPUBLISHED->value,
        ]);
    }

    #[Test]
    public function a_published_item_is_visible_on_the_welcome_page(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id, 'status' => ItemStatus::UNPUBLISHED]);

        // First, ensure it's not visible when unpublished
        $this->get(route('welcome'))->assertDontSee($item->title);

        // Then, publish it
        $this->actingAs($user)->post(route('items.publish', $item));

        // Now, it should be visible
        $this->get(route('welcome'))->assertSee($item->title);
    }

    #[Test]
    public function guests_cannot_publish_items(): void
    {
        $item = Item::factory()->create(['status' => ItemStatus::UNPUBLISHED]);

        $response = $this->post(route('items.publish', $item));

        $response->assertRedirect(route('login'));
    }
}
