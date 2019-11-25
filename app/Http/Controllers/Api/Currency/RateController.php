<?php

namespace App\Http\Controllers\Api\Currency;

use App\Currency;
use App\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Currency $currency, ExchangeRate $rate)
    {
        //TODO::: implement all rates related to selected currency
        return response()->json($currency);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Currency $currency)
    {
        $dateTime = $request->get('timestamp');
        $date = is_numeric($dateTime) ? Carbon::createFromTimestamp($dateTime) : new Carbon($dateTime);

        $existRecord = ExchangeRate::where('currency_id', $currency->id)
                        ->where('date', $date->format('Y-m-d 00:00:00'))->first();
        //good style to check, suggest we dont have unique key on MySQL or it is very generic validation logic
        //later we can more to requestForm validation like we did in ClientRequest of UserController
        if ($existRecord) {
            throw new \Exception('Already exists!');
        }
        //but we also add Unique KEY on MYSQL to prevent concurrency
        $rate = ExchangeRate::create([
            'currency_id' => $currency->id,
            'rate' => $request->get('rate'),
            'date' => $date->format('Y-m-d 00:00:00'),
        ]);

        return response()->json($rate);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency, ExchangeRate $rate)
    {
        return response()->json([
            'currency' => $currency,
            'rate' => $rate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency, ExchangeRate $rate)
    {
        $rate->rate = $request->get('rate');
        $rate->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExchangeRate  $exchangeRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExchangeRate $exchangeRate)
    {
        //
    }
}
