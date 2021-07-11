<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{

  private $service;

  public function __construct(UserService $service)
  {
    $this->service = $service;
  }

  /**
   * @OA\Get(
   *   path="/user/current",
   *   summary="現在のユーザーを返す",
   *   tags={"user"},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(ref="#/components/schemas/User"),
   *   ),
   *   @OA\Response(
   *     response="default",
   *     description="Unexpected Error",
   *     @OA\JsonContent(
   *       type="object",
   *       @OA\Property(
   *         property="message",
   *         type="string",
   *         description="レスポンスパラメータの例を記載"
   *       )
   *     )
   *   )
   * )
   */
  public function currentUser()
  {
    $currentUser = $this->service->currentUser();
    return !!$currentUser ? response($currentUser) : response()->json();
  }
}
