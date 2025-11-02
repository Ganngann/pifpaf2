<?php

namespace App\Models;

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
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
