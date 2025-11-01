<?php

namespace App\Models;

use App\Enums\ItemStatus;
use Illuminate\Database\Eloquent\Builder;
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => ItemStatus::class,
    ];

    /**
     * Scope a query to only include available items.
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', ItemStatus::AVAILABLE);
    }

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

    /**
     * Obtenir l'image principale de l'annonce.
     */
    public function primaryImage()
    {
        return $this->hasOne(ItemImage::class)
            ->where('is_primary', true)
            ->withDefault(function ($itemImage, $item) {
                // Si aucune image n'est marquée comme principale,
                // on retourne la première image de la galerie.
                if ($item->images()->exists()) {
                    return $item->images()->first();
                }
            });
    }
}
