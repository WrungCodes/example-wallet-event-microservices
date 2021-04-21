<?php

namespace App\Logics;

use Illuminate\Support\Str;
use App\Events\WalletCreatedEvent;
use App\Events\WalletDebitedEvent;
use Illuminate\Support\Facades\DB;
use App\Events\WalletCreditedEvent;
use App\Events\WalletDebitFailedEvent;
use App\Events\WalletCreateFailedEvent;
use App\Events\WalletCreditFailedEvent;

class WalletMessageLogic
{
    private $message;

    public function __construct($message)
    {
        $this->message = json_decode($message);
    }

    public function handle()
    {
        $request_type = $this->message->data->event;

        if($request_type == 'CREDIT_WALLET')
        {
            return $this->credit($this->message->data);
        }

        if($request_type == 'DEBIT_WALLET')
        {
            return $this->debit($this->message->data);
        }

        // if($request_type == 'reverse')
        // {
        //     return $this->reverse($this->message);
        // }

        if($request_type == 'CREATE_WALLET')
        {
            return $this->create($this->message->data);
        }
    }

    private function create($message)
    {
        $ex_mssg = [
            'event' => 'CREATE_WALLET',
            'info' => [
                'email' => 'user@example.com',
                'name' => 'exampleusername', 
                'phone' => '090...'
            ],
        ];

        $current_wallet = DB::table('wallets')->where('email', $message->info->email)->first();

        if(!$current_wallet)
        {
            $wallet = DB::table('wallets')->insert([
                'identifier' => $identifier = Str::random(40),
                'email' => $message->info->email,
                'balance' => 0.00
            ]);

            event(new WalletCreatedEvent([
                'event' => 'WALLET_CREATED',
                'message' => 'wallet created successfully',
                'data' => [
                    'wallet' => [
                        'identifier' => $identifier,
                        'email' => $message->info->email,
                        'balance' => 0.00
                    ],
                ]
            ]));

            return;
        }

        event(new WalletCreateFailedEvent([
            'event' => 'WALLET_CREATE_FAILED',
            'message' => 'wallet create failed',
            'error' => [
                'type' => 'dupicate_email',
                'reason' => 'wallet with email '.$message->info->email.' already exists'
            ],
            'info' => [
                'email' => $message->info->email,
                'name' => $message->info->name,
                'phone' => $message->info->phone
            ]
        ]));
        return;
    }

    private function credit($message)
    {
        $ex_mssg = [
            'event' => 'CREDIT_WALLET',
            'info' => [
                'email' => 'user@example.com', 
                'name' => 'exampleusername', 
                'phone' => '090...',
                'amount' => 5000
            ],
            'wallet' => [
                'identifier' => 'cfcfvgjnjhgcdzssdfhhjnh'
            ]
        ];

        $current_wallet = DB::table('wallets')->where('identifier', $message->wallet->identifier)->first();

        if(!$current_wallet)
        {
            event(new WalletCreditFailedEvent([
                'event' => 'WALLET_CREDIT_FAILED',
                'message' => 'wallet credit failed',
                'error' => [
                    'type' => 'incorrect_identifier',
                    'reason' => 'wallet with identifier '.$message->wallet->identifier. ' not found'
                ],
                'wallet' => [
                    'identifier' => $message->wallet->identifier,
                    'email' => $message->info->email
                ]
            ]));
            return;
        }

        $wallet = DB::table('wallets')->where('id', $current_wallet->id)->update([
            'balance' => $balance = $current_wallet->balance + floatval($message->info->amount)
        ]);

        event(new WalletCreditedEvent([
            'event' => 'WALLET_CREDITED',
            'message' => 'wallet credited',
            'data' => [
                'credited_amount' => floatval($message->info->amount),
                'wallet' => [
                    'identifier' => $message->wallet->identifier,
                    'email' => $current_wallet->email,
                    'balance' => $balance
                ],
            ]
        ]));

        return;
    }

    private function debit($message)
    {
        $ex_mssg = [
            'event' => 'DEBIT_WALLET',
            'info' => [
                'email' => 'user@example.com', 
                'name' => 'exampleusername', 
                'phone' => '090...',
                'amount' => 5000
            ],
            'wallet' => [
                'identifier' => 'cfcfvgjnjhgcdzssdfhhjnh'
            ]
        ];
        
        $current_wallet = DB::table('wallets')->where('identifier', $message->wallet->identifier)->first();

        if(!$current_wallet)
        {
            event(new WalletDebitFailedEvent([
                'event' => 'WALLET_DEBIT_FAILED',
                'message' => 'wallet debit failed',
                'error' => [
                    'type' => 'incorrect_identifier',
                    'reason' => 'wallet with identifier '.$message->wallet->identifier. ' not found'
                ],
                'wallet' => [
                    'identifier' => $message->wallet->identifier,
                    'email' => $message->info->email
                ]
            ]));
            return;
        }

        if($current_wallet->balance < floatval($message->info->amount))
        {
            event(new WalletDebitFailedEvent([
                'event' => 'WALLET_DEBIT_FAILED',
                'message' => 'wallet debit failed',
                'error' => [
                    'type' => 'insuffient_funds',
                    'reason' => 'wallet has insuffient funds'
                ],
                'wallet' => [
                    'identifier' => $message->wallet->identifier,
                    'email' => $message->info->email,
                    'balance' => $current_wallet->balance
                ]
            ]));
            return;
        }

        $wallet = DB::table('wallets')->where('id', $current_wallet->id)->update([
            'balance' => $balance = $current_wallet->balance - floatval($message->info->amount)
        ]);

        event(new WalletDebitedEvent([
            'event' => 'WALLET_DEBITED',
            'message' => 'wallet debited',
            'data' => [
                'debited_amount' => floatval($message->info->amount),
                'wallet' => [
                    'identifier' => $message->wallet->identifier,
                    'email' => $current_wallet->email,
                    'balance' => $balance
                ],
            ]
        ]));
        return;
    }

    private function reverse($message)
    {
        $current_transaction = DB::table('wallet_histories')->where('reference', $message->transaction->reference)->first();

        if(!$current_transaction)
        {
            event();
            return;
        }

        if($current_transaction->type == 'reversal')
        {
            event();
            return;
        }

        $current_wallet = DB::table('wallets')->where('wallet_id', $current_transaction->wallet_id)->first();

        $balance = $current_transaction->type == 'credit' ? 
            ($current_wallet->balance - floatval($message->user->credit_amount)) :
            ($current_wallet->balance + floatval($message->user->credit_amount)) ;

        $wallet = DB::table('wallets')->where('id', $current_wallet->id)->update([
            'balance' => $balance
        ]);

        event();
        return;
    }
}