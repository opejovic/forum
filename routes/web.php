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

Auth::routes(['verify' => true]);
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/threads', 'ThreadsController@index')->name('threads.index');
Route::get('/threads/create', 'ThreadsController@create')->name('threads.create')->middleware(['auth', 'verified']);
Route::get('/threads/{channel}', 'ThreadsController@index')->name('channel.threads');
Route::post('/threads', 'ThreadsController@store')->name('threads.store')->middleware('auth');
Route::get('/threads/{channelId}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy')->name('threads.delete')->middleware('auth');

Route::get('/threads/{channelId}/{thread}/replies', 'RepliesController@index')->name('replies.index');
Route::post('/threads/{channelId}/{thread}/replies', 'RepliesController@store')->name('replies.store')->middleware('auth');

Route::group(['prefix' => 'replies', 'middleware' => 'auth'], function () {
	Route::patch('{reply}', 'RepliesController@update')->name('replies.update');
	Route::delete('{reply}', 'RepliesController@destroy')->name('replies.delete');

	Route::post('{reply}/favorites', 'ReplyFavoritesController@store')->name('reply.favorite');
	Route::delete('{reply}/favorites', 'ReplyFavoritesController@destroy')->name('reply.unfavorite');
});

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profiles.show');

Route::group(['middleware' => 'auth'], function () {
	Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index')->name('notifications.index');
	Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy')->name('notifications.delete');

	Route::post('/threads/{channelId}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')
		->name('thread.subscribe');
	Route::delete('/threads/{channelId}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')
		->name('thread.unsubscribe');
});

Route::get('/api/users', 'Api\UsersController@index');
Route::middleware('auth')->post('/api/users/{user}/avatar', 'Api\UserAvatarsController@store')->name('avatar.store');