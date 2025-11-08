<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Models\Dispute;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisputeCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_see_dispute_button_on_transaction_page()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::PAYMENT_RECEIVED]);

        $response = $this->actingAs($buyer)->get(route('transactions.show', $transaction));

        $response->assertStatus(200);
        $response->assertSee('Signaler un problème');
    }

    public function test_seller_cannot_see_dispute_button_on_transaction_page()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::PAYMENT_RECEIVED]);

        $response = $this->actingAs($seller)->get(route('transactions.show', $transaction));

        $response->assertStatus(200);
        $response->assertDontSee('Signaler un problème');
    }

    public function test_buyer_cannot_see_dispute_button_if_status_is_not_payment_received()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::COMPLETED]);

        $response = $this->actingAs($buyer)->get(route('transactions.show', $transaction));

        $response->assertStatus(200);
        $response->assertDontSee('Signaler un problème');
    }

    public function test_buyer_can_create_a_dispute()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::PAYMENT_RECEIVED]);

        $response = $this->actingAs($buyer)->post(route('disputes.store', $transaction), [
            'reason' => 'This is a test reason for the dispute. It is long enough to pass validation.',
        ]);

        $response->assertRedirect(route('transactions.show', $transaction));
        $this->assertDatabaseHas('disputes', [
            'transaction_id' => $transaction->id,
            'user_id' => $buyer->id,
            'reason' => 'This is a test reason for the dispute. It is long enough to pass validation.',
        ]);
        $this->assertEquals(TransactionStatus::DISPUTED, $transaction->fresh()->status);
    }
}
