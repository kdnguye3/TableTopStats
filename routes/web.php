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

Route::get('/', function () {
    return redirect(route('players.index'));
});
Route::get('games/json', 'GameController@json');
Route::post('games/json', 'GameController@json');
Route::post('games/{game}/json', 'GameController@gameJson');
Route::resource('games', 'GameController');

Route::get('players/json', 'PlayerController@json');
Route::post('players/json', 'PlayerController@json');
Route::post('players/{player}/json', 'PlayerController@playerJson');
Route::resource('players', 'PlayerController');

Route::resource('plays', 'PlayController');
