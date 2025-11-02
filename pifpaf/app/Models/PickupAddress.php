<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'street',
        'city',
        'postal_code',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
