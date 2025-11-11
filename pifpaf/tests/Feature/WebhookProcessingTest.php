<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Notifications\DeliveryNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WebhookProcessingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_transaction_status_based_on_sendcloud_webhook()
    {
        // 1. Arrange
        $transaction = Transaction::factory()->create([
            'sendcloud_parcel_id' => 12345,
            'status' => TransactionStatus::SHIPPED,
        ]);

        $payload = [
            'action' => 'parcel_status_changed',
            'parcel' => [
                'id' => 12345,
                'status' => [
                    'id' => 11, // En route
                    'message' => 'The parcel is on its way to the final destination.'
                ]
            ]
        ];

        $secret = config('sendcloud.secret_key');
        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        // 2. Act
        $response = $this->withHeaders(['Sendcloud-Signature' => $signature])
                         ->postJson(route('webhooks.sendcloud'), $payload);

        // 3. Assert
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::IN_TRANSIT->value,
        ]);
    }

    /** @test */
    public function it_sends_a_notification_to_the_buyer_when_the_parcel_is_delivered()
    {
        // 1. Arrange
        Notification::fake();

        $buyer = User::factory()->create();
        $offer = Offer::factory()->for($buyer)->create();
        $transaction = Transaction::factory()->for($offer)->create([
            'sendcloud_parcel_id' => 67890,
            'status' => TransactionStatus::IN_TRANSIT,
        ]);

        $payload = [
            'action' => 'parcel_status_changed',
            'parcel' => [
                'id' => 67890,
                'status' => [
                    'id' => 12, // Delivered
                    'message' => 'The parcel has been delivered.'
                ]
            ]
        ];

        $secret = config('sendcloud.secret_key');
        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        // 2. Act
        $this->withHeaders(['Sendcloud-Signature' => $signature])
             ->postJson(route('webhooks.sendcloud'), $payload);

        // 3. Assert
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::DELIVERED->value,
        ]);

        Notification::assertSentTo(
            [$buyer], DeliveryNotification::class
        );
    }
}
