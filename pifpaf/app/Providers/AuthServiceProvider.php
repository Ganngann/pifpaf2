<?php

namespace App\Providers;

use App\Models\Item;
use App\Policies\ItemPolicy;
use App\Models\PickupAddress;
use App\Policies\PickupAddressPolicy;
use App\Models\ShippingAddress;
use App\Policies\ShippingAddressPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Item::class => ItemPolicy::class,
        PickupAddress::class => PickupAddressPolicy::class,
        ShippingAddress::class => ShippingAddressPolicy::class,
        \App\Models\Conversation::class => \App\Policies\ConversationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
