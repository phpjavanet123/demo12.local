<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
//for IDE PhpStorm
use Illuminate\Routing\Route as RouteBind;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function() {
    Route::resource('users', 'UserController')->middleware('administrator');
    Route::resource('transactions', 'TransactionController')->only(['index']);
});

Route::group(['prefix' => 'api', 'as' => 'api.'], function() {
    Route::apiResource('users', 'Api\UserController');
    Route::apiResource('wallets', 'Api\WalletController');
    Route::apiResource('wallets.transfers', 'Api\Wallet\TransferController');
    Route::apiResource('currencies', 'Api\CurrencyController');
    Route::apiResource('currencies.rates', 'Api\Currency\RateController');
    Route::apiResource('wallets.withdraws', 'Api\Wallet\WithdrawController');
    Route::apiResource('wallets.deposits', 'Api\Wallet\DepositController');
});

Route::model('transfer', Transaction::class);

Route::bind('rate', function ($dateTime,  RouteBind $route)  {
    $currency = $route->parameter('currency');

    $date = is_numeric($dateTime) ? Carbon::createFromTimestamp($dateTime) : new Carbon($dateTime);
    return  $currency->rates()->where('date', $date->format('Y-m-d 00:00:00'))->firstOrFail();
});

Route::bind('currency', function ($value) {
    return App\Currency::where('code', $value)->first() ?? abort(404); //->firstOrFail(); more simple way => 404
});

//php artisan route:list
