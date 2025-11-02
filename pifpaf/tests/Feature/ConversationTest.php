<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_start_a_conversation()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($buyer)->post(route('conversations.store'), ['item_id' => $item->id]);

        $response->assertRedirect();
        $this->assertDatabaseHas('conversations', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);
    }

    public function test_a_user_can_see_their_conversations()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $conversation = Conversation::factory()->create(['buyer_id' => $user->id, 'item_id' => $item->id]);


        $response = $this->actingAs($user)->get(route('conversations.index'));

        $response->assertOk();
        $response->assertSee($conversation->item->title);
    }

    public function test_a_user_can_send_a_message_in_a_conversation()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create(['buyer_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('messages.store', $conversation), [
            'content' => 'This is a test message.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => 'This is a test message.',
        ]);
    }

    public function test_a_user_cannot_see_a_conversation_they_are_not_part_of()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $response = $this->actingAs($user)->get(route('conversations.show', $conversation));

        $response->assertForbidden();
    }

    public function test_a_user_cannot_send_a_message_in_a_conversation_they_are_not_part_of()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $response = $this->actingAs($user)->post(route('messages.store', $conversation), [
            'content' => 'This is a test message.',
        ]);

        $response->assertForbidden();
    }
}
