<?php

namespace Tests\Feature\Policies;

use App\Models\Conversation;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_view_conversation()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $conversation = Conversation::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($buyer);

        $this->assertTrue($buyer->can('view', $conversation));
    }

    public function test_seller_can_view_conversation()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $conversation = Conversation::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($seller);

        $this->assertTrue($seller->can('view', $conversation));
    }

    public function test_other_user_cannot_view_conversation()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $conversation = Conversation::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($otherUser);

        $this->assertFalse($otherUser->can('view', $conversation));
    }

    public function test_buyer_can_update_conversation()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $conversation = Conversation::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($buyer);

        $this->assertTrue($buyer->can('update', $conversation));
    }

    public function test_seller_can_update_conversation()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $conversation = Conversation::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($seller);

        $this->assertTrue($seller->can('update', $conversation));
    }

    public function test_other_user_cannot_update_conversation()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $conversation = Conversation::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($otherUser);

        $this->assertFalse($otherUser->can('update', $conversation));
    }

    public function test_user_cannot_delete_conversation()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $conversation));
    }
}
