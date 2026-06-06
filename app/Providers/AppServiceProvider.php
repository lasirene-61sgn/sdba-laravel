<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\Inflector\InflectorFactory;
use App\Services\RealTimeNotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RealTimeNotificationService::class, function($app){ 
            return new RealTimeNotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load broadcasting routes
        
    }
}
