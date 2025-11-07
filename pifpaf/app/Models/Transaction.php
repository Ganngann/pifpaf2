<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'amount',
        'wallet_amount',
        'card_amount',
        'status',
        'pickup_code',
        'shipping_address_id',
        'sendcloud_parcel_id',
        'tracking_code',
        'label_url',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }
}
