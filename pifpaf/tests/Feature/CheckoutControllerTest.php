<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_summary_for_accepted_offer()
    {
        $user = User::factory()->create();
        $itemOwner = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $itemOwner->id]);
        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status' => 'accepted'
        ]);
        $address = Address::factory()->create(['user_id' => $user->id, 'is_for_delivery' => true]);

        $response = $this->actingAs($user)->get(route('checkout.summary', $offer));

        $response->assertStatus(200);
        $response->assertViewIs('checkout.summary');
    }

    public function test_user_cannot_access_summary_for_unaccepted_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)->get(route('checkout.summary', $offer));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('error');
    }

    public function test_user_cannot_access_summary_of_other_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'accepted'
        ]);

        $response = $this->actingAs($user)->get(route('checkout.summary', $offer));

        $response->assertStatus(403);
    }

    public function test_user_can_access_success_page()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id, 'status' => 'accepted']);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $response = $this->actingAs($user)->get(route('checkout.success', $transaction));

        $response->assertStatus(200);
        $response->assertViewIs('checkout.success');
    }

    public function test_user_cannot_access_success_page_of_other_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $otherUser->id, 'status' => 'accepted']);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $response = $this->actingAs($user)->get(route('checkout.success', $transaction));

        $response->assertStatus(403);
    }
}
