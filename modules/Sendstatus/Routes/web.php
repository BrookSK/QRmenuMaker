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
    'namespace' => 'Modules\Sendstatus\Http\Controllers'
], function () {
    Route::prefix('sendstatus')->group(function() {
        Route::post('/submit', 'Main@submit')->name('sendstatus.submit');
       
    });
});
