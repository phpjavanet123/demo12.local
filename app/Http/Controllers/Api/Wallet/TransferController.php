<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Services\TransferService;
use App\Transaction;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferController extends Controller
{
    protected $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Wallet $wallet)
    {
        $transactions = Transaction::whereWalletId($wallet->id)->get();
        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Wallet $wallet)
    {
        //TODO::: Add validation for Request to be sure we have all required params
        $transaction = $this->transferService->createTransaction(
            $wallet,
            Wallet::whereNumber($request->get('to_wallet_number'))->firstOrFail(),
            $request->get('amount'),
            $request->get('currency_code')
        );

        return response()->json([
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wallet $wallet, Transaction $transaction)
    {
        //HERE I renamed to: Transaction $transaction) - BUT LARAVEL ROUTING WAS ABLE To RESOLVE in web.php
        //Route::model('transfer', Transaction::class);


        //check if transaction belong to wallet
        //check if status == pending (after create ENUM of statuses)
        if ($transaction->status != 'pending') {
            //Or we can ignore it, and say only it was exucuted and return date -> all array of transaction
            //amount took from, putted amount ON, substract from account and add to wallet another account
            throw new \Exception('Transaction already was executed');
        }

        //WE PASS "status" => "execute" OR: "status" => "rollback"
        if ($request->get('status') == 'execute') {
            $transactionExecuted = $this->transferService->transfer($transaction->id, Carbon::now());
            return response()->json([
                'transaction_id' => $transactionExecuted->id,
                'status' => $transactionExecuted->status,
            ]);
        }

        throw new \Exception('Not implemented!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        throw new \Exception('Not implemented!');
    }
}
