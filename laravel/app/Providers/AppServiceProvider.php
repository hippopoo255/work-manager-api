<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    \Schema::defaultStringLength(191);
    $this->app->bind(
      \Auth0\Login\Contract\Auth0UserRepository::class,
      \Auth0\Login\Repository\Auth0UserRepository::class
    );
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    if (request()->isSecure()) {
      \URL::forceScheme('https');
    }
  }
}
