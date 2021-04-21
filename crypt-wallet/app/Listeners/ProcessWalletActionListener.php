<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\Jobs\HandleWalletActionJob;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\RecievedWalletActionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessWalletActionListener
{
    public function __construct()
    {
        //
    }

    public function handle(RecievedWalletActionEvent $event)
    {
        dispatch(new HandleWalletActionJob($event->message));
    }
}
