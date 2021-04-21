<?php

namespace App\Events;

class WalletCreditFailedEvent extends Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
