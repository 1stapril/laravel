<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeCtrl;


Route::get('/', function () {
    return redirect()->route('stripe');
});

Route::GET('stripe', [StripeCtrl::class, 'stripe'])->name('stripe');
Route::POST('stripe', [StripeCtrl::class, 'stripePost'])->name('stripe.post');
