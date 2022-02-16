<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OrganizationFilter
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param string|null $guard
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $request->fullUrlWithQuery([
      'createdBy:organization_id' => 1,
    ]);
    return $next($request);
  }
}
