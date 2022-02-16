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

Route::name('admin.')->group(function () {
  // 認証中の管理ユーザーを返却
  Route::get('/current', 'AdminController@currentAdmin')->name('currentAdmin');

  // 管理者認証手続
  Route::namespace('Auth')->group(function () {
    //     Route::post('/register', 'RegisterController@register')->name('register');
    //     Route::post('/login', 'LoginController@login')->name('login');
    //
    //     Route::middleware('auth:admin')->group(function() {
    //       Route::post('/logout', 'LoginController@logout')->name('logout');
    //     });
  });

  // 管理者認証が必要なAPI
  Route::middleware('auth:admin')->group(function () {
    // TODO: 組織情報更新のエンドポイント
    // ...
    Route::middleware('orgExists')->group(function () {
      // 組織情報の登録後にアクセス可能なエンドポイント
    });
  });
});
