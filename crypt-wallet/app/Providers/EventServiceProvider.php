<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],

        \App\Events\RecievedWalletActionEvent::class => [
            \App\Listeners\ProcessWalletActionListener::class,
        ],

        \App\Events\WalletCreatedEvent::class => [
            \App\Listeners\SendEventToWalletMessageQueueListener::class,
        ],

        \App\Events\WalletCreateFailedEvent::class => [
            \App\Listeners\SendEventToWalletMessageQueueListener::class,
        ],

        \App\Events\WalletCreditedEvent::class => [
            \App\Listeners\SendEventToWalletMessageQueueListener::class,
        ],

        \App\Events\WalletCreditFailedEvent::class => [
            \App\Listeners\SendEventToWalletMessageQueueListener::class,
        ],

        \App\Events\WalletDebitedEvent::class => [
            \App\Listeners\SendEventToWalletMessageQueueListener::class,
        ],

        \App\Events\WalletDebitFailedEvent::class => [
            \App\Listeners\SendEventToWalletMessageQueueListener::class,
        ],
    ];
}
