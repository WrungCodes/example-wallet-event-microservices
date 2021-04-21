<?php

/*
|--------------------------------------------------------------------------
| Queue
|--------------------------------------------------------------------------
|
|
*/

return [
    'connections' => [
        'rabbitmq' => [
            'host' => env('RABBITMQ_HOST'),
            'port' => env('RABBITMQ_PORT'),
            'user' => env('RABBITMQ_USER'),
            'vhost' => env('RABBITMQ_VHOST'),
            'password' => env('RABBITMQ_PASSWORD'),

            'queues' => [
                'wallet_action' => 'wallet_action.wallet',
                
                'wallet_response_main' => 'wallet_action.main',
                'wallet_response_email' => 'wallet_action.email',
            ],

            'exchanges' => [
                'wallet_action_response_exchange' => 'wallet_action_response.exchange',
                'wallet_action_exchange' => 'wallet_action.exchange',
            ],
        ],
    ],

    'rabbit' => [
        'driver' => 'rabbitmq',
        'connection' => PhpAmqpLib\Connection\AMQPLazyConnection::class,
        'hosts' => [
            [
                'host' => env('RABBITMQ_HOST'),
                'port' => env('RABBITMQ_PORT'),
                'user' => env('RABBITMQ_USER'),
                'vhost' => env('RABBITMQ_VHOST'),
                'pass' => env('RABBITMQ_PASSWORD'),
            ],
        ],

        'options' => [

        ],

        'exchanges' => [
            'wallet_action' => 'wallet_action'
        ],

        'queue' => [
            'wallet' => 'wallet'
        ]
    ],
];