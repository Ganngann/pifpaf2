<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'role',
        'banned_at',
        'notification_preferences',
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
            'banned_at' => 'datetime',
            'notification_preferences' => 'array',
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

    /**
     * Get all of the user's addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the user's pickup addresses.
     */
    public function pickupAddresses(): HasMany
    {
        return $this->addresses()->where('is_for_pickup', true);
    }

    /**
     * Get the user's shipping addresses.
     */
    public function shippingAddresses(): HasMany
    {
        return $this->addresses()->where('is_for_delivery', true);
    }

    /**
     * Vérifie si l'utilisateur est un administrateur.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Vérifie si l'utilisateur est banni.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    /**
     * Vérifie si l'utilisateur souhaite recevoir un type de notification.
     *
     * @param string $notificationType
     * @return bool
     */
    public function wantsNotification(string $notificationType): bool
    {
        return $this->notification_preferences[$notificationType] ?? true;
    }

    /**
     * Get the bank accounts for the user.
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get the withdrawal requests for the user.
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }
}
