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
    'namespace' => 'Modules\Manager\Http\Controllers'
], function () {
    Route::prefix('manager')->group(function() {

   
            Route::get('/list', 'Main@index')->name('manager.index');
            Route::get('/{table}/edit', 'Main@edit')->name('manager.edit');
            Route::get('/create', 'Main@create')->name('manager.create');
            Route::post('/', 'Main@store')->name('manager.store');
            Route::put('/{table}', 'Main@update')->name('manager.update');
            Route::get('/del/{table}', 'Main@destroy')->name('manager.delete');
            Route::get('/loginas/{manager}', 'Main@loginas')->name('manager.loginas');
            
        


    });
});
