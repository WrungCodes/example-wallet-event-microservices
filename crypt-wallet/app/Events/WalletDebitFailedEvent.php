<?php

namespace App\Events;

class WalletDebitFailedEvent extends Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
