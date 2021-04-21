<?php

namespace App\Providers;

use App\rabbitmsg\AmqpManager;
use Illuminate\Support\ServiceProvider;

class AmqpServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * AmqpManager is made singleton so that the Class is destructed once
         * Which will make sure that the connections are also closed once through the following
         */
        $this->app->singleton(AmqpManager::class, function ($app) {
            return new AmqpManager($app);
        });
    }
}
