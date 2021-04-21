<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\rabbitmsg\AmqpManager;
use PhpAmqpLib\Message\AMQPMessage;
use App\Events\WalletDebitFailedEvent;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $amqpManager;

    public function __construct(AmqpManager $amqpManager)
    {
        $this->amqpManager = $amqpManager;
    }

    public function load(Request $request)
    {
        $connection = $this->amqpManager->getConn('rabbitmq');
        $channel = $this->amqpManager->getChann('rabbitmq');

        $exchange = config('msgqueue.connections.rabbitmq.exchanges.wallet_action_exchange');

        $queue1 = config('msgqueue.connections.rabbitmq.queues.wallet_action');
        // $queue2 = config('msgqueue.connections.rabbitmq.queues.wallet_response_email');


        $this->amqpManager->exchangeDeclare($channel, $exchange, AMQPExchangeType::FANOUT, false, true, false);
        $this->amqpManager->queueDeclare($channel, $queue1, false, true, false, false);
        // $this->amqpManager->queueDeclare($channel, $queue2, false, true, false, false);
        $this->amqpManager->queueBindMany($channel, [$queue1], $exchange);

        $this->amqpManager->publish($channel, $exchange, new AMQPMessage(json_encode($request->all()), array('content_type' => 'application/json')));

        $this->amqpManager->close('rabbitmq', $channel);

        return response()->json();
    }
}
