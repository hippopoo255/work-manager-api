<?php

namespace App\Services\Auth;

use Illuminate\Database\Eloquent\Model;
use App\Services\JWTVerifierService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CognitoGuard implements Guard
{
  use GuardHelpers;

  /**
   * @var $JWTVerifier
   */
  private $JWTVerifier;

  /**
   * @var $userProvider
   */
  private $request;

  /**
   * @var $userProvider
   */
  private $userProvider;

  private $builder;

  /**
   * @param JWTVerifierService $JWTVerifier
   * @param UserProvider $userProvider
   */
  public function __construct(
    JWTVerifierService $JWTVerifier,
    Request $request,
    UserProvider $userProvider,
    Authenticatable $builder
  ) {
    $this->JWTVerifier = $JWTVerifier;
    $this->request = $request;
    $this->userProvider = $userProvider;
    $this->builder = $builder;
  }

  public function user()
  {
    if ($this->user) {
      return $this->user;
    }

    $decoded = $this->JWTVerifier->decode();

    if ($decoded) {
      $result = $this->userProvider->retrieveByCredentials(['cognito_sub' => $decoded->sub]);
      if (is_null($result)) {
        $registered = $this->create($decoded);
        $result = $this->userProvider->retrieveByCredentials(['cognito_sub' => $registered->cognito_sub]);
      }
      return $result;
    }

    return null;
  }

  public function validate(array $credentials = [])
  {
    throw new \RuntimeException('Cognito guard cannot be used for credential based authentication.');
  }

  private function create($decoded)
  {
    $cognito_username = 'cognito:username';
    $custom_family_name_kana = 'custom:family_name_kana';
    $custom_given_name_kana = 'custom:given_name_kana';

    $author = get_class($this->builder)::updateOrCreate(['cognito_sub' => $decoded->sub], [
      'cognito_sub' => $decoded->sub,
      'email' => $decoded->email ?? '',
      'login_id' => $decoded->{$cognito_username},
      'family_name' => $decoded->family_name,
      'family_name_kana' => $decoded->{$custom_family_name_kana},
      'given_name' => $decoded->given_name,
      'given_name_kana' => $decoded->{$custom_given_name_kana},
      'password' => \Hash::make(\Str::random(32)),
      'role_id' => 1,
    ]);
    if ($author) {
      $author->update([
        'created_by' => $author->id,
      ]);
    }
    return $author;
  }
}
