<?php

namespace App\Services;

use Firebase\JWT\JWK;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class JWTVerifierService
{
  private $region;
  private $userpoolId;

  public function __construct($userpoolId = '', $region = '')
  {
    $this->userpoolId = empty($userpoolId) ? config('cognito.user.userpoolId') : $userpoolId;
    $this->region = empty($region) ? config('cognito.user.region') : $region;
  }

  public function decode()
  {
    $jwt = \Request::header('Authorization');
    if (!$jwt) {
      return null;
    }
    $tokensArray = explode('.', $jwt);
    if (count($tokensArray) !== 3) {
      return null;
    }
    [$headb64, $_, $_] = $tokensArray;

    $jwks = $this->fetchJWKs();

    try {
      $kid = $this->getKid($headb64);
      $jwk = $this->getJWK($jwks, $kid);
      $alg = $this->getAlg($jwks, $kid);
      $decodedData = JWT::decode($jwt, $jwk, [$alg]);
      return $decodedData;
    } catch (\RuntimeException $exception) {
      return null;
    }
  }

  private function fetchJWKs(): array
  {
    $downloadUrl = 'https://cognito-idp.' . $this->region . '.amazonaws.com/' . $this->userpoolId . '/.well-known/jwks.json';
    $response = Http::get($downloadUrl);
    return json_decode($response->getBody()->getContents(), true) ?: [];
  }

  /**
   * @param string $headb64
   * @return string
   */
  private function getKid(string $headb64): string
  {
    $headb64 = json_decode(JWT::urlsafeB64Decode($headb64), true);
    if (array_key_exists('kid', $headb64)) {
      return $headb64['kid'];
    }
    throw new \RuntimeException();
  }

  private function getJWK(array $jwks, string $kid)
  {
    $keys = JWK::parseKeySet($jwks);
    if (array_key_exists($kid, $keys)) {
      return $keys[$kid];
    }
    throw new \RuntimeException();
  }

  private function getAlg(array $jwks, string $kid)
  {
    if (!array_key_exists('keys', $jwks)) {
      throw new \RuntimeException();
    }

    foreach ($jwks['keys'] as $key) {
      if ($key['kid'] === $kid && array_key_exists('alg', $key)) {
        return $key['alg'];
      }
    }
    throw new \RuntimeException();
  }
}
