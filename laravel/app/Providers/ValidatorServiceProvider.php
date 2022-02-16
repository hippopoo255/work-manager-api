<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class ValidatorServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap services.
   *
   * @return void
   */
  public function boot()
  {
    Validator::extend(
      'postalcode',
      function ($attribute, $value, $parameters, $validator) {
        return preg_match('/\A[0-9]{5,7}\z/', $value);
      }
    );
    Validator::extend(
      'katakana',
      function ($attribute, $value, $parameters, $validator) {
        return preg_match('/\A[ァ-ヴーｦ-ﾟ]+\z/u', $value);
      }
    );
    Validator::extend(
      'tel',
      function ($attribute, $value, $parameters, $validator) {
        return preg_match('/\A[0-9]{10,11}\z/', $value);
      }
    );
  }
}
