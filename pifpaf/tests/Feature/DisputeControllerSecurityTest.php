<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisputeControllerSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_buyer_or_seller_can_create_dispute(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $otherUser = User::factory()->create();

        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $response = $this->actingAs($otherUser)->post(route('disputes.store', $transaction), [
            'reason' => 'This is a valid reason that is long enough.',
        ]);

        $response->assertForbidden();
    }
}
