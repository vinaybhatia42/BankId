<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BankIdController;

Route::controller(PaymentController::class)->group(function(){
    // Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});
Route::post('/authenticate-user', [BankIdController::class, 'checkBank']);
Route::post('/bankid/status', [BankIdController::class, 'checkStatus']);