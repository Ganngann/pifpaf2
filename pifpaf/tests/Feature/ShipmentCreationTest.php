<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\Address;
use App\Enums\ItemStatus;
use App\Enums\TransactionStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShipmentCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_seller_can_create_a_shipment_for_a_transaction(): void
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $address = Address::factory()->for($buyer)->create(['is_for_delivery' => true]);

        $item = Item::factory()->for($seller)->create([
            'status' => ItemStatus::SOLD,
            'weight' => 500, // in grams
            'length' => 20,
            'width' => 15,
            'height' => 10,
        ]);

        $offer = Offer::factory()->for($item)->for($buyer)->create();

        $transaction = Transaction::factory()->for($offer)->create([
            'status' => TransactionStatus::PAYMENT_RECEIVED,
            'address_id' => $address->id,
        ]);

        // Fake the Sendcloud API response
        Http::fake([
            'https://panel.sendcloud.sc/api/v2/parcels' => Http::response([
                'parcel' => [
                    'id' => 12345,
                    'tracking_number' => 'TRACK123',
                    'label' => [
                        'label_printer' => 'https://example.com/label.pdf'
                    ]
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
            'label_url' => 'https://example.com/label.pdf',
            'status' => TransactionStatus::SHIPPED->value,
        ]);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://panel.sendcloud.sc/api/v2/parcels';
        });
    }

    #[Test]
    public function it_handles_sendcloud_api_errors_gracefully(): void
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $address = Address::factory()->for($buyer)->create(['is_for_delivery' => true]);
        $item = Item::factory()->for($seller)->create(['status' => ItemStatus::SOLD]);
        $offer = Offer::factory()->for($item)->for($buyer)->create();
        $transaction = Transaction::factory()->for($offer)->create([
            'status' => TransactionStatus::PAYMENT_RECEIVED,
            'address_id' => $address->id,
        ]);

        // Fake the Sendcloud API response for a failure
        Http::fake([
            'https://panel.sendcloud.sc/api/v2/parcels' => Http::response([
                'error' => [
                    'code' => 400,
                    'message' => 'Invalid or missing data'
                ]
            ], 400)
        ]);

        // 2. Act
        $response = $this->actingAs($seller)->post(route('transactions.ship', $transaction));

        // 3. Assert
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Erreur Sendcloud : Invalid or missing data');

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::PAYMENT_RECEIVED->value, // Status should not have changed
            'sendcloud_parcel_id' => null, // No parcel ID should be saved
        ]);
    }
}
