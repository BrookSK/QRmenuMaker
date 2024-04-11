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
    'namespace' => 'Modules\Asaas\Http\Controllers'
], function () {
    Route::prefix('asaas')->group(function() {
        Route::get('/execute/{status}/{order}', 'Main@executePayment')->name('asaas.execute');
        Route::get('/pay/{order}', 'Main@pay')->name('asaas.pay');
    });
});
