<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Enums\TransactionStatus;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddTrackingNumberTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_seller_can_add_a_tracking_number_to_a_transaction()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::SHIPPED]);

        $trackingNumber = '123456789';

        $response = $this->actingAs($seller)
            ->patch(route('transactions.addTracking', $transaction), [
                'tracking_code' => $trackingNumber,
            ]);

        $response->assertRedirect(route('transactions.sales'));
        $response->assertSessionHas('success', 'Numéro de suivi ajouté avec succès.');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tracking_code' => $trackingNumber,
            'status' => TransactionStatus::IN_TRANSIT->value,
        ]);
    }

    #[Test]
    public function an_unauthorized_user_cannot_add_a_tracking_number()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $unauthorizedUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::SHIPPED]);

        $trackingNumber = '123456789';

        $response = $this->actingAs($unauthorizedUser)
            ->patch(route('transactions.addTracking', $transaction), [
                'tracking_code' => $trackingNumber,
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tracking_code' => null,
            'status' => TransactionStatus::SHIPPED->value,
        ]);
    }

    #[Test]
    public function a_tracking_number_is_required()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::SHIPPED]);

        $response = $this->actingAs($seller)
            ->patch(route('transactions.addTracking', $transaction), [
                'tracking_code' => '',
            ]);

        $response->assertSessionHasErrors('tracking_code');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tracking_code' => null,
            'status' => TransactionStatus::SHIPPED->value,
        ]);
    }
}
