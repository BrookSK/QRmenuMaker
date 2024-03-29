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

Route::prefix('pdf-invoice')->group(function() {
    Route::get('/', 'PdfInvoiceController@index');
});

Route::group([
    'middleware' =>[ 'web','impersonate'],
    'namespace' => 'Modules\PdfInvoice\Http\Controllers'
], function () {
    Route::prefix('pdfinvoice')->group(function() {
        Route::get('/{order}', 'Main@index')->name('pdfinvoice');
    });
});