<?php

namespace App\Events;

class WalletCreditedEvent extends Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
