<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Notifications\DeliveryNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendcloudWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Verify the signature
        $signature = $request->header('Sendcloud-Signature');
        $secret = config('sendcloud.secret_key');
        $calculatedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($signature, $calculatedSignature)) {
            Log::warning('Invalid Sendcloud webhook signature received.');
            abort(403, 'Invalid signature.');
        }

        // 2. Process the webhook payload
        $payload = $request->json()->all();

        if ($payload['action'] === 'parcel_status_changed') {
            $parcel = $payload['parcel'];
            $transaction = Transaction::where('sendcloud_parcel_id', $parcel['id'])->first();

            if ($transaction) {
                // Map Sendcloud status to our application status
                $newStatus = $this->mapStatus($parcel['status']['id']);
                if ($newStatus) {
                    $transaction->update(['status' => $newStatus]);
                    Log::info("Transaction {$transaction->id} status updated to {$newStatus} by Sendcloud webhook.");

                    // If the item is delivered, notify the buyer
                    if ($newStatus === 'delivered') {
                        $buyer = $transaction->offer->user;
                        $buyer->notify(new DeliveryNotification($transaction));
                    }
                }
            } else {
                Log::warning("Received webhook for unknown parcel ID: {$parcel['id']}");
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Map Sendcloud status ID to application status string.
     *
     * @param int $sendcloudStatusId
     * @return string|null
     */
    private function mapStatus(int $sendcloudStatusId): ?string
    {
        // See https://docs.sendcloud.com/api/v2/docs/parcels-statuses-events for all statuses
        return match ($sendcloudStatusId) {
            11 => 'in_transit',       // En route
            12 => 'delivered',        // Delivered
            default => null,          // For other statuses, do nothing
        };
    }
}
