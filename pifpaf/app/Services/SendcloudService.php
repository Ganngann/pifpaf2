<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SendcloudService
{
    protected $publicKey;
    protected $secretKey;
    protected $baseUrl = 'https://panel.sendcloud.sc/api/v2/';

    public function __construct()
    {
        $this->publicKey = config('sendcloud.public_key');
        $this->secretKey = config('sendcloud.secret_key');
    }

    /**
     * Build the authenticated HTTP client.
     */
    protected function client()
    {
        return Http::withBasicAuth($this->publicKey, $this->secretKey)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }

    /**
     * Create a new parcel in Sendcloud.
     *
     * @param \App\Models\Item $item
     * @param \App\Models\ShippingAddress $shippingAddress
     * @param int $shippingMethodId
     * @return \Illuminate\Http\Client\Response
     */
    public function createParcel($item, $shippingAddress, $shippingMethodId)
    {
        // Defensive coding: Ensure country and weight have default values.
        $country = !empty($shippingAddress->country) ? $shippingAddress->country : 'FR'; // Default to France
        $weightInGrams = $item->weight ?: 1; // Default to 1 gram if weight is not set
        $weightInKg = $weightInGrams / 1000;

        // Ensure weight is at least the minimum required by Sendcloud (0.001 kg)
        if ($weightInKg < 0.001) {
            $weightInKg = 0.001;
        }

        $payload = [
            'parcel' => [
                'name' => $shippingAddress->name,
                'address' => $shippingAddress->street,
                'city' => $shippingAddress->city,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $country,
                'email' => $shippingAddress->user->email, // Assuming the user relationship is loaded
                'weight' => $weightInKg,
                'length' => $item->length,
                'width' => $item->width,
                'height' => $item->height,
                'shipment' => [
                    'id' => $shippingMethodId,
                ],
                'request_label' => true,
            ],
        ];

        return $this->client()->post($this->baseUrl . 'parcels', $payload);
    }
}
