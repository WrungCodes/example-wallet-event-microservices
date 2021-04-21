<?php

namespace App\Events;

class RecievedWalletActionEvent extends Event
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }
}
