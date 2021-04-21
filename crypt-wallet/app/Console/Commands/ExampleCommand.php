<?php

namespace App\Console\Commands;

use App\rabbitmsg\AmqpManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Events\RecievedWalletActionEvent;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AmqpManager $am)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}