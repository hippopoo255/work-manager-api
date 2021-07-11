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
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
   */
  public function currentUser()
  {
    $currentUser = $this->service->currentUser();
    return !!$currentUser ? response($currentUser) : response()->json();
  }
}
