<?php

namespace Tests\Feature\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $item));
    }

    public function test_non_owner_cannot_update_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $item));
    }

    public function test_owner_can_delete_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('delete', $item));
    }

    public function test_non_owner_cannot_delete_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $item));
    }

    public function test_anybody_can_view_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->assertTrue($user->can('view', $item));
    }

    public function test_guest_can_view_item()
    {
        $item = Item::factory()->create();
        $this->assertTrue((new \App\Policies\ItemPolicy)->view(null, $item));
    }
}
