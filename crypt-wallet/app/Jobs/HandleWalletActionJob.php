<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Logics\WalletMessageLogic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleWalletActionJob extends Job
{
    private $message;

    public function __construct($message)
    {
        $this->onQueue('wallet_actions');
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return (new WalletMessageLogic($this->message))->handle();
    }
}
