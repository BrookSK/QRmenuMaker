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

Route::group([
    'namespace' => 'Modules\Paypal\Http\Controllers'
], function () {
    Route::prefix('paypal')->group(function() {
        Route::get('/execute', 'Main@executePayment')->name('paypal.execute');
        Route::get('/cancel', 'Main@cancelPayment')->name('paypal.cancel');
    });
});


