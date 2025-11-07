<?php

namespace App\Providers;

use App\Models\Item;
use App\Policies\ItemPolicy;
use App\Models\Address;
use App\Policies\AddressPolicy;
use App\Models\Transaction;
use App\Policies\TransactionPolicy;
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
        Address::class => AddressPolicy::class,
        \App\Models\Conversation::class => \App\Policies\ConversationPolicy::class,
        Transaction::class => TransactionPolicy::class,
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
