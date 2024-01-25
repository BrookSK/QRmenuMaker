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
    'namespace' => 'Modules\Staff\Http\Controllers'
], function () {
    Route::prefix('staff')->group(function() {

   
            Route::get('/list', 'Main@index')->name('staff.index');
            Route::get('/{table}/edit', 'Main@edit')->name('staff.edit');
            Route::get('/create', 'Main@create')->name('staff.create');
            Route::post('/', 'Main@store')->name('staff.store');
            Route::put('/{table}', 'Main@update')->name('staff.update');
            Route::get('/del/{table}', 'Main@destroy')->name('staff.delete');
            Route::get('/loginas/{staff}', 'Main@loginas')->name('staff.loginas');
            
        


    });
});