<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::auth();

Route::get('/', 'HomeController@index');
Route::get('myCollection', 'MovieController@myCollection');
Route::get('movieCategory/{movieCategory}', 'MovieController@movieCategory');
Route::get('tvCategory/{tvCategory}', 'MovieController@tvCategory');
Route::get('find', 'MovieController@find');
Route::get('movie/{id}', 'MovieController@movie');
Route::get('tv/{id}', 'MovieController@tvShow');
Route::post('movie/{id}/updateMovieCollection', 'MovieController@updateMovieCollection');
Route::post('tv/{id}/updateTVCollection', 'MovieController@updateTVCollection');

Route::post('movieCategory/new', 'MovieController@newMovieCategory');
Route::post('tvCategory/new', 'MovieController@newTVCategory');
Route::delete('movieCategory/{id}/delete', 'MovieController@deleteMovieCategory');
Route::delete('tvCategory/{id}/delete', 'MovieController@deleteTVCategory');
