<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'seller_id',
    ];

    /**
     * Obtenir l'article associé à la conversation.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Obtenir l'utilisateur acheteur de la conversation.
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Obtenir l'utilisateur vendeur de la conversation.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Obtenir les messages de la conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Obtenir le dernier message de la conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
