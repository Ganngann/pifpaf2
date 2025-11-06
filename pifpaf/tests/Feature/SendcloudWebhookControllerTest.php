<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SendcloudWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Configure la clé secrète pour les tests
        config(['sendcloud.secret_key' => 'test-secret']);
    }

    private function getValidPayload(int $parcelId, int $statusId, string $statusMessage): array
    {
        return [
            'action' => 'parcel_status_changed',
            'parcel' => [
                'id' => $parcelId,
                'status' => [
                    'id' => $statusId,
                    'message' => $statusMessage,
                ],
            ],
        ];
    }

    private function getSignature(string $payload): string
    {
        return hash_hmac('sha256', $payload, config('sendcloud.secret_key'));
    }

    public function test_it_handles_valid_webhook_and_updates_transaction_status()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => \App\Models\Item::factory()->create(['user_id' => $seller->id])->id]);

        $transaction = Transaction::factory()->create([
            'offer_id' => $offer->id,
            'sendcloud_parcel_id' => 12345,
            'status' => 'initiated'
        ]);

        $payload = $this->getValidPayload(12345, 11, 'En route'); // 11 = in_transit
        $jsonPayload = json_encode($payload);
        $signature = $this->getSignature($jsonPayload);

        Log::shouldReceive('info')
            ->once()
            ->with("Transaction {$transaction->id} status updated to in_transit by Sendcloud webhook.");

        $response = $this->withHeaders(['Sendcloud-Signature' => $signature])
                         ->postJson(route('webhooks.sendcloud'), $payload);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'in_transit',
        ]);
    }

    public function test_it_rejects_request_with_invalid_signature()
    {
        $payload = $this->getValidPayload(12345, 11, 'En route');
        $jsonPayload = json_encode($payload);
        $invalidSignature = 'invalid-signature';

        Log::shouldReceive('warning')
            ->once()
            ->with('Invalid Sendcloud webhook signature received.');

        $response = $this->withHeaders(['Sendcloud-Signature' => $invalidSignature])
                         ->postJson(route('webhooks.sendcloud'), $payload);

        $response->assertStatus(403);
    }

    public function test_it_logs_warning_for_unknown_parcel_id()
    {
        $payload = $this->getValidPayload(99999, 11, 'En route'); // ID de colis inconnu
        $jsonPayload = json_encode($payload);
        $signature = $this->getSignature($jsonPayload);

        Log::shouldReceive('warning')
            ->once()
            ->with('Received webhook for unknown parcel ID: 99999');

        $response = $this->withHeaders(['Sendcloud-Signature' => $signature])
                         ->postJson(route('webhooks.sendcloud'), $payload);

        $response->assertStatus(200);
    }

    public function test_it_ignores_unmapped_status()
    {
        $transaction = Transaction::factory()->create([
            'sendcloud_parcel_id' => 54321,
            'status' => 'initiated'
        ]);

        $payload = $this->getValidPayload(54321, 999, 'Statut inconnu'); // ID de statut non mappé
        $jsonPayload = json_encode($payload);
        $signature = $this->getSignature($jsonPayload);

        $response = $this->withHeaders(['Sendcloud-Signature' => $signature])
                         ->postJson(route('webhooks.sendcloud'), $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'initiated', // Le statut ne doit pas changer
        ]);
    }
}
