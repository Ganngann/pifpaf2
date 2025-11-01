<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GoogleAiService;
use Tests\Fakes\FakeGoogleAiService;

class DuskTestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('dusk.local')) {
            $this->app->singleton(GoogleAiService::class, FakeGoogleAiService::class);
        }
    }
}
