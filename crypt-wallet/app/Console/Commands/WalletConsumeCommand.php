<?php

namespace App\Console\Commands;

use App\rabbitmsg\AmqpManager;
use Illuminate\Console\Command;
use App\Events\RecievedWalletActionEvent;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class WalletConsumeCommand extends Command
{
    protected $signature = 'wallet:consume';

    protected $description = 'Receives wallet infos from message broker';

    private $amqpManager;

    public function __construct(AmqpManager $amqpManager)
    {
        parent::__construct();
        $this->amqpManager = $amqpManager;
    }

    public function handle()
    {
        $connection = $this->amqpManager->getConn('rabbitmq');
        $channel = $this->amqpManager->getChann('rabbitmq');

        $consumerTag = 'consumer'.getmypid();

        $queue = config('msgqueue.connections.rabbitmq.queues.wallet_action');
        $this->amqpManager->queueDeclare($channel, $queue, false, true, false, false);

        $this->amqpManager->consume($channel, $queue, $consumerTag, false, false, false, false, function($message) {
            $this->info($message->getBody());
            event(new RecievedWalletActionEvent($message->getBody()));
            $message->ack();
        });
        
        $this->info('Started Listing on '.$queue. ' queue');
        
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}

// 'fanout_exchange.queue2'
// Only use when you want to start afresh
// $this->am->queueDeclare($channel, 'fanout_exchange.queue1', false, true, false, false);
// $this->am->exchangeDeclare($channel, 'fanout_exchange', AMQPExchangeType::FANOUT, false, true, false);
// $this->am->queueBindMany($channel, ['fanout_exchange.queue1'], 'fanout_exchange');