<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {
  // 認証中の一般ユーザーを返却
  Route::get('/user/current', 'UserController@currentUser')->name('currentUser');
  // 認証手続
  Route::namespace('Auth')->group(function() {
    Route::post('/register', 'RegisterController@register')->name('register');
    Route::post('/login', 'LoginController@login')->name('login');

    Route::middleware('auth')->group(function() {
      Route::post('/logout', 'LoginController@logout')->name('logout');
    });
  });

  // 一般認証が必要なAPI
  Route::middleware('auth')->group(function() {
    // チャットルーム
    Route::get('/author/chat_room', 'ChatRoomController@findByOwner')->name('chatRoom.findByOwner');
  });
});
