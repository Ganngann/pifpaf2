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
        $payload = [
            'parcel' => [
                'name' => $shippingAddress->name,
                'address' => $shippingAddress->street,
                'city' => $shippingAddress->city,
                'postal_code' => $shippingAddress->postal_code,
                'country' => $shippingAddress->country,
                'email' => $shippingAddress->user->email, // Assuming the user relationship is loaded
                'weight' => $item->weight / 1000, // Convert grams to kg for the API
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
