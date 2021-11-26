<?php

namespace App\Http\Middleware;

use Auth0\Login\Contract\Auth0UserRepository;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Exception\InvalidTokenException;
use Closure;

class CheckJWT
{
  protected $userRepository;

  /**
   * CheckJWT constructor.
   *
   * @param Auth0UserRepository $userRepository
   */
  public function __construct(Auth0UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $auth0 = \App::make('auth0');
    // リクエストからBearerトークンを取得する
    $accessToken = $request->bearerToken() ?? '';
    try {
      // トークンをデコードし、JWTを検証する
      $tokenInfo = $auth0->decodeJWT($accessToken);
      // トークンからユーザの情報を取得する
      $user = $this->userRepository->getUserByDecodedJWT($tokenInfo);
      if (!$user) {
        return response()->json(["message" => "Unauthorized user"], 401);
      }
    } catch (InvalidTokenException $e) {
      return response()->json(["InvalidTokenExceptionMessage" => $e->getMessage()], 401);
    } catch (CoreException $e) {
      return response()->json(["CoreExceptionMessage" => $e->getMessage()], 401);
    }

    return $next($request);
  }
}
