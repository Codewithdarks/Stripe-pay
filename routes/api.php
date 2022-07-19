<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('form', [\App\Http\Controllers\ApiController::class, 'StripeCustomer'])->name('stripe.customer');
Route::post('pay', [\App\Http\Controllers\ApiController::class, 'StripePaymentMethod'])->name('stripe.pay');
Route::post('check', [\App\Http\Controllers\ApiController::class,'StripeAttachCard'])->name('stripe.check');
Route::post('intent',[\App\Http\Controllers\ApiController::class, 'StripeIntent'])->name('stripe.intent');
