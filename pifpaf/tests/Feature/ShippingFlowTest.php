<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\ShippingAddress;
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
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $buyer->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'status' => 'paid']);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => 'completed', 'shipping_address_id' => $shippingAddress->id]);

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
            'status' => 'shipping_initiated',
        ]);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://panel.sendcloud.sc/api/v2/parcels' &&
                   $request->method() == 'POST';
        });
    }
}
