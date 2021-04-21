<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\rabbitmsg\AmqpManager;
use App\Jobs\HandleWalletActionJob;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\RecievedWalletActionEvent;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEventToWalletMessageQueueListener
{
    public function __construct()
    {
        //
    }

    public function handle($event_data)
    {
        $amqpManager = app(AmqpManager::class);

        $connection = $amqpManager->getConn('rabbitmq');
        $channel = $amqpManager->getChann('rabbitmq');

        $exchange = config('msgqueue.connections.rabbitmq.exchanges.wallet_action_response_exchange');

        $queue1 = config('msgqueue.connections.rabbitmq.queues.wallet_response_main');
        $queue2 = config('msgqueue.connections.rabbitmq.queues.wallet_response_email');


        $amqpManager->exchangeDeclare($channel, $exchange, AMQPExchangeType::FANOUT, false, true, false);
        $amqpManager->queueDeclare($channel, $queue1, false, true, false, false);
        $amqpManager->queueDeclare($channel, $queue2, false, true, false, false);
        $amqpManager->queueBindMany($channel, [$queue1, $queue2], $exchange);

        $amqpManager->publish($channel, $exchange, new AMQPMessage(json_encode((array)$event_data), array('content_type' => 'application/json')));

        $amqpManager->close('rabbitmq', $channel);
    }
}
