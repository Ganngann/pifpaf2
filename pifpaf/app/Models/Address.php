<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'street',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_for_pickup',
        'is_for_delivery',
    ];

    protected $casts = [
        'is_for_pickup' => 'boolean',
        'is_for_delivery' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
