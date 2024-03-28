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

Route::prefix('expenses')->group(function() {
    Route::get('/', 'ExpensesController@index');
});

Route::group([
    'middleware' => ['web','impersonate'],
    'namespace' => 'Modules\Expenses\Http\Controllers'
], function () {
    Route::prefix('expenses')->group(function() {

        

         // Expenses
         Route::prefix('expenses')->name('expenses.expenses.')->group(function() {
            Route::get('/', 'ExpensesController@index')->name('index');
            Route::get('/{expense}/edit', 'ExpensesController@edit')->name('edit');
            Route::get('/expense', 'ExpensesController@create')->name('create');
            Route::post('/', 'ExpensesController@store')->name('store');
            Route::put('/{expense}', 'ExpensesController@update')->name('update');
            Route::get('/del/{expense}', 'ExpensesController@destroy')->name('delete');
        });

        // Categories
        Route::prefix('categories')->name('expenses.categories.')->group(function() {
            Route::get('/', 'CategoriesController@index')->name('index');
            Route::get('/{category}/edit', 'CategoriesController@edit')->name('edit');
            Route::get('/category', 'CategoriesController@create')->name('create');
            Route::post('/', 'CategoriesController@store')->name('store');
            Route::put('/{category}', 'CategoriesController@update')->name('update');
            Route::get('/del/{category}', 'CategoriesController@destroy')->name('delete');
        });

        // Vendors
        Route::prefix('vendors')->name('expenses.vendors.')->group(function() {
            Route::get('/', 'VendorsController@index')->name('index');
            Route::get('/{vendor}/edit', 'VendorsController@edit')->name('edit');
            Route::get('/vendor', 'VendorsController@create')->name('create');
            Route::post('/', 'VendorsController@store')->name('store');
            Route::put('/{vendor}', 'VendorsController@update')->name('update');
            Route::get('/del/{vendor}', 'VendorsController@destroy')->name('delete');
        });


    });
});
