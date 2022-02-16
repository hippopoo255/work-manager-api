<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Role;
use App\Enums\Role as RoleEnum;
use Auth;
use App\Services\Auth\CognitoGuard;
use App\Services\JWTVerifierService;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The policy mappings for the application.
   *
   * @var array
   */
  protected $policies = [
    'App\Models\Activity' => 'App\Policies\ActivityPolicy',
    'App\Models\Blog' => 'App\Policies\BlogPolicy',
    'App\Models\BlogComment' => 'App\Policies\BlogCommentPolicy',
    'App\Models\ChatMessage' => 'App\Policies\ChatMessagePolicy',
    'App\Models\ChatRoom' => 'App\Policies\ChatRoomPolicy',
    'App\Models\DocumentFolder' => 'App\Policies\DocumentFolderPolicy',
    'App\Models\DocumentFile' => 'App\Policies\DocumentFilePolicy',
    'App\Models\MeetingRecord' => 'App\Policies\MeetingRecordPolicy',
    'App\Models\Schedule' => 'App\Policies\SchedulePolicy',
    'App\Models\Task' => 'App\Policies\TaskPolicy',
    'App\Models\User' => 'App\Policies\UserPolicy',
    'App\Models\Organization' => 'App\Policies\OrganizationPolicy',
  ];

  /**
   * Register any authentication / authorization services.
   *
   * @return void
   */
  public function boot()
  {
    $this->registerPolicies();
    // Auth
    Auth::extend('cognito', function ($app, $name, array $config) {
      return new CognitoGuard(
        new JWTVerifierService(),
        $app['request'],
        Auth::createUserProvider($config['provider']),
        new User(),
      );
    });
    Auth::extend('cognito:admin', function ($app, $name, array $config) {
      return new CognitoGuard(
        new JWTVerifierService(config('cognito.admin.userpoolId'), config('cognito.admin.region')),
        $app['request'],
        Auth::createUserProvider($config['provider']),
        new Admin(),
      );
    });

    // Gate
    Gate::define('overManager', function ($user) {
      return $user instanceof User && $user->role->value >= RoleEnum::value('manager');
    });
  }
}
