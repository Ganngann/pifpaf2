<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'price',
        'status',
        'pickup_available',
    ];

    /**
     * Obtenir l'utilisateur propriétaire de l'annonce.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir les offres pour l'annonce.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Obtenir les images de l'annonce.
     */
    public function images()
    {
        return $this->hasMany(ItemImage::class)->orderBy('order');
    }
}
