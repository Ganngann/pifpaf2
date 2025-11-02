<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'wallet',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Obtenir les annonces de l'utilisateur.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Obtenir les offres faites par l'utilisateur.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function aiRequests()
    {
        return $this->hasMany(AiRequest::class);
    }

    public function pickupAddresses()
    {
        return $this->hasMany(PickupAddress::class);
    }
}
