<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Auth0IndexController extends Controller
{
  public function login()
  {
    $authorize_params = [
      'scope' => 'openid email email_verified',
      // Use the key below to get an Access Token for your API.
      // 'audience' => config('laravel-auth0.api_identifier'),
    ];

    return \App::make('auth0')->login(
      null,
      null,
      $authorize_params,
      'code'
    );
  }

  public function logout()
  {
    \Auth::logout();
    $logoutUrl = sprintf(
      'https://%s/v2/logout?client_id=%s&returnTo=%s',
      config('laravel-auth0.domain'),
      config('laravel-auth0.client_id'),
      config('laravel-auth0.redirect_url', 'http://localhost:8080') . '/api'
    );

    return \Redirect::intended($logoutUrl);
  }
}
