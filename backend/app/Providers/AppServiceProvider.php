<?php

namespace App\Providers;

use App\Services\External\OMDbApi\OMDbApiClient;
use App\Services\RedisService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RedisService::class);

        $this->app->singleton(OMDbApiClient::class, static fn () => new OMDbApiClient(
            baseUrl: config('services.omdb_api.base_url'),
            token: config('services.omdb_api.token'),
            timeout: (int) config('services.omdb_api.timeout'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

}
