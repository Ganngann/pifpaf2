<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste qu'un utilisateur peut envoyer un message dans une conversation à laquelle il participe.
     */
    public function test_user_can_send_message_in_their_conversation(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        // Crée une conversation entre l'acheteur et le vendeur
        $conversation = Conversation::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($buyer);

        $response = $this->post(route('messages.store', $conversation), [
            'content' => 'Bonjour, est-ce toujours disponible ?',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'user_id' => $buyer->id,
            'content' => 'Bonjour, est-ce toujours disponible ?',
        ]);
    }

    /**
     * Teste qu'un utilisateur ne peut pas envoyer de message dans la conversation de quelqu'un d'autre.
     */
    public function test_user_cannot_send_message_in_other_user_conversation(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $anotherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        // Crée une conversation entre l'acheteur et le vendeur
        $conversation = Conversation::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($anotherUser);

        $response = $this->post(route('messages.store', $conversation), [
            'content' => 'Je ne devrais pas être ici.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $conversation->id,
            'content' => 'Je ne devrais pas être ici.',
        ]);
    }

    /**
     * Teste que le contenu du message est requis.
     */
    public function test_message_content_is_required(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        // Crée une conversation entre l'acheteur et le vendeur
        $conversation = Conversation::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $this->actingAs($buyer);

        $response = $this->post(route('messages.store', $conversation), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    /**
     * Teste qu'un utilisateur non authentifié ne peut pas envoyer de message.
     */
    public function test_unauthenticated_user_cannot_send_message(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        // Crée une conversation entre l'acheteur et le vendeur
        $conversation = Conversation::create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->post(route('messages.store', $conversation), [
            'content' => 'Ceci ne devrait pas fonctionner.',
        ]);

        $response->assertRedirect('/login');
    }
}
