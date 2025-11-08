<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'amount',
        'status',
        'delivery_method',
    ];

    /**
     * Obtenir l'utilisateur qui a fait l'offre.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir l'article pour lequel l'offre a été faite.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Obtenir la transaction associée à l'offre.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
