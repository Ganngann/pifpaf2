<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_offer()
    {
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        $response = $this->post(route('offers.store', $item), ['amount' => 10]);

        $response->assertRedirect('/login');
    }

    public function test_user_can_submit_offer_on_another_users_item()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'price' => 20,
            'delivery_available' => true,
        ]);

        $response = $this->actingAs($buyer)->post(route('offers.store', $item), [
            'amount' => 15,
            'delivery_method' => 'delivery',
        ]);

        $response->assertRedirect(route('items.show', $item));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('offers', [
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'amount' => 15,
            'status' => 'pending',
            'delivery_method' => 'delivery',
        ]);
    }

    public function test_user_cannot_submit_offer_on_own_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'delivery_available' => true,
        ]);

        $response = $this->actingAs($user)->post(route('offers.store', $item), [
            'amount' => 10,
            'delivery_method' => 'delivery',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseMissing('offers', ['item_id' => $item->id]);
    }

    public function test_seller_can_see_offers_on_dashboard()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'amount' => 10,
        ]);

        $response = $this->actingAs($seller)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeText('Offres reçues');
        $response->assertSeeText($buyer->name);
        $response->assertSee(number_format(10, 2, ',', ' '));
    }

    public function test_seller_can_accept_offer()
    {
        $seller = User::factory()->create();
        $buyer1 = User::factory()->create();
        $buyer2 = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer1 = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer1->id, 'status' => 'pending']);
        $offer2 = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer2->id, 'status' => 'pending']);

        $response = $this->actingAs($seller)->patch(route('offers.accept', $offer1));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('offers', ['id' => $offer1->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('offers', ['id' => $offer2->id, 'status' => 'rejected']);
    }

    public function test_seller_can_reject_offer()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'status' => 'pending']);

        $response = $this->actingAs($seller)->patch(route('offers.reject', $offer));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'status' => 'rejected']);
    }

    public function test_unauthorized_user_cannot_accept_offer()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $rando = User::factory()->create(); // Random user
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);

        // Acting as the buyer
        $this->actingAs($buyer)->patch(route('offers.accept', $offer))->assertForbidden();

        // Acting as a random user
        $this->actingAs($rando)->patch(route('offers.accept', $offer))->assertForbidden();
    }
}
