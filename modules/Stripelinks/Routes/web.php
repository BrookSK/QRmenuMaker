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
    'namespace' => 'Modules\Stripelinks\Http\Controllers'
], function () {
    Route::prefix('stripelinks')->group(function() {
        Route::get('/execute/{order}', 'Main@onSiteStripeCheckout')->name('stripelinks.onsitecheckout');
        Route::get('/success/{ordermd}', 'Main@success')->name('stripelinks.success');
        Route::get('/cancel/{ordermd}', 'Main@cancel')->name('stripelinks.cancel');
    });
});

