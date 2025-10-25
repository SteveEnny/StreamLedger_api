<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Support\ServiceProvider;
use App\Services\KafkaProducerService;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(KafkaProducerService::class, function ($app) {
            return new KafkaProducerService();
        });
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(UrlGenerator $url)
    {
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
    }
    // public function boot(): void
    // {
    //     Transaction::observe(TransactionObserver::class);
    // }
}