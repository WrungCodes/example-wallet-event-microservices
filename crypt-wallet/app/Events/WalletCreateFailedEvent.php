<?php

namespace App\Events;

class WalletCreateFailedEvent extends Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
