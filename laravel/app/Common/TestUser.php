<?php

namespace App\Common;

class TestUser
{
  public static function hasId()
  {
    return !!config('app.test_id');
  }

  public static function id()
  {
    $user = \DB::table('users')->where('login_id', config('app.test_id'))->get()->first();
    if (!!$user) {
      return $user->id;
    } else {
      return null;
    }
  }
}
