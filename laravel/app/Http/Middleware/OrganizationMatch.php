<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OrganizationMatch
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next, $bind = 'id', $belongs = 'createdBy')
  {
    // リソースが属している組織と、リクエストユーザの所属組織が一致しない場合は403
    $resource = $request->route($bind);
    if ($resource && $this->mismatchOrg($resource, $belongs)) {
      return response()->json([
        'message' => 'Organization doesn\'t match.',
        'status' => 'ORGANIZATION_MISMATCH',
      ], 403);
    }

    return $next($request);
  }

  /**
   * Undocumented function
   *
   * @param Model $resource
   * @param string $belongs
   * @return bool
   */
  private function mismatchOrg($resource, $belongs): bool
  {
    $belongsOrgId = $resource instanceof Authenticatable ? $resource->organization_id : $resource->{$belongs}->organization_id;
    return $belongsOrgId !== Auth::user()->organization_id;
  }
}
