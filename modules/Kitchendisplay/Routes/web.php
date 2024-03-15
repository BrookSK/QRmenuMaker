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
    'middleware' =>[ 'web','impersonate'],
    'namespace' => 'Modules\Kitchendisplay\Http\Controllers'
], function () {
    Route::prefix('kitchendisplay')->group(function() {
            Route::get('/kdss', 'Main@index')->name('kitchendisplay.index');

    });
});
