<?php

namespace App\Providers;

use App\Domain\Catalog\Contracts\CurrencyRepository;
use App\Domain\Catalog\Contracts\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCurrencyRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductRepository::class,
            EloquentProductRepository::class,
        );

        $this->app->bind(
            CurrencyRepository::class,
            EloquentCurrencyRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
