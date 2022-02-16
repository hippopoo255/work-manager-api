<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OrganizationExists
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    // 組織情報を登録していない場合は403
    if (!Auth::user()->organization) {
      return response()->json([
        'message' => 'Organization doesn\'t exist.',
        'status' => 'ORGANIZATION_EMPTY',
      ], 403);
    }

    return $next($request);
  }
}
