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
    'namespace' => 'Modules\Cloner\Http\Controllers'
], function () {
    Route::prefix('cloner')->group(function() {
        Route::get('/doit/{newid}/{oldid}', 'Main@index')->name('cloner.index');
        Route::get('/cloneitem/{item}', 'Main@cloneItem')->name('cloner.cloneitem');
        
    });
});
