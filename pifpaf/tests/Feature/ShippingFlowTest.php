<?php

namespace Tests\Feature;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Item;
use App\Models\Offer;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShippingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_can_create_a_shipment_for_a_completed_sale()
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'weight' => 500, 'width' => 10, 'height' => 10, 'length' => 10]);
        $address = Address::factory()->create(['user_id' => $buyer->id, 'type' => AddressType::DELIVERY]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'status' => 'paid']);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::COMPLETED, 'address_id' => $address->id]);

        // Fake the Sendcloud API response
        Http::fake([
            'panel.sendcloud.sc/api/v2/parcels' => Http::response([
                'parcel' => [
                    'id' => 12345,
                    'tracking_number' => 'TRACK123',
                    'label' => ['label_printer' => 'http://example.com/label.pdf'],
                ]
            ], 200)
        ]);

        // 2. Act
        $response = $this->actingAs($seller)->post(route('transactions.ship', $transaction));

        // 3. Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Envoi créé avec succès.');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'sendcloud_parcel_id' => 12345,
            'tracking_code' => 'TRACK123',
            'label_url' => 'http://example.com/label.pdf',
            'status' => TransactionStatus::SHIPPING_INITIATED->value,
        ]);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://panel.sendcloud.sc/api/v2/parcels' &&
                   $request->method() == 'POST';
        });
    }

    public function test_cannot_create_shipment_for_transaction_without_shipping_address()
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'status' => 'paid']);
        // Create a transaction without a shipping address
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => TransactionStatus::COMPLETED, 'address_id' => null]);

        // We don't need to fake the API here, as it should not be called.
        Http::fake();

        // 2. Act
        $response = $this->actingAs($seller)->post(route('transactions.ship', $transaction));

        // 3. Assert
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cette transaction ne nécessite pas d\'expédition car elle n\'a pas d\'adresse de livraison.');

        // Assert the transaction was not updated in the database
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'sendcloud_parcel_id' => null,
            'tracking_code' => null,
            'status' => TransactionStatus::COMPLETED->value, // Status should remain unchanged
        ]);

        // Assert that no request was sent to the Sendcloud API
        Http::assertNothingSent();
    }
}
