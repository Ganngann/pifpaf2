<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'street',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'type' => AddressType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
