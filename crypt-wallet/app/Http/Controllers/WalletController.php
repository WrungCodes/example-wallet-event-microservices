<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\Request;
use App\rabbitmsg\AmqpManager;
use Illuminate\Support\Facades\DB;
use PhpAmqpLib\Message\AMQPMessage;
use App\Events\WalletDebitFailedEvent;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class WalletController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {

    }

    public function getWallet(Request $request, $identity, $value)
    {
        if(!in_array($identity, array('email', 'identifier')) || $value === null)
        {
            return response()->json(['message' => 'invalidIndentity'], 400);
        }

        try {
            $wallet = DB::table('wallets')->select('email', 'identifier', 'balance')->where($identity, '=', $value)->first();

            if($wallet == null)
            {
                return response()->json(['message' => 'notFound'], 404);
            }

            return response()->json($wallet, 200);

        } 
        catch (\Throwable $th) 
        {
            return response()->json(['message' => 'serverError'], 500);
        }

    }
}
