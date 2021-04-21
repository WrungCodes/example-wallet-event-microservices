<?php

namespace App\Events;

class WalletDebitedEvent extends Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
