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

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index')->name('threads.index');
Route::get('/threads/create', 'ThreadsController@create')->name('threads.create')->middleware('auth');
Route::get('/threads/{channel}', 'ThreadsController@index')->name('channel.threads');
Route::post('/threads', 'ThreadsController@store')->name('threads.store')->middleware('auth');
Route::get('/threads/{channelId}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy')->name('threads.delete')->middleware('auth');

Route::get('/threads/{channelId}/{thread}/replies', 'RepliesController@index')->name('replies.index');
Route::post('/threads/{channelId}/{thread}/replies', 'RepliesController@store')->name('replies.store')->middleware('auth');
Route::patch('/replies/{reply}', 'RepliesController@update')->name('replies.update')->middleware('auth');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.delete')->middleware('auth');

Route::post('/replies/{reply}/favorites', 'ReplyFavoritesController@store')->name('reply.favorite')->middleware('auth');
Route::delete('/replies/{reply}/favorites', 'ReplyFavoritesController@destroy')->name('reply.unfavorite')->middleware('auth');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profiles.show');