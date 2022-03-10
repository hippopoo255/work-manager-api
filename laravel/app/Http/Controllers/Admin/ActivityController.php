<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService as Service;
use App\Models\User;
use App\Models\Admin;

use Illuminate\Http\Request;

class ActivityController extends Controller
{
  private $service;

  public function __construct(Service $service)
  {
    $this->service = $service;
  }

  /**
   * @param Request $request
   * @param User $id
   * @return \Illuminate\Http\Response
   */
  public function findByCreatedUser(Request $request, User $id)
  {
    return response($this->service->findByCreatedUser($request->query(), $id), 200);
  }

  /**
   * @param Request $request
   * @param Admin $id
   * @return \Illuminate\Http\Response
   */
  public function findByCreatedAdmin(Request $request, Admin $id)
  {
    return response($this->service->findByCreatedUser(['authenticatable' => 'admin'], $id->bUser), 200);
  }
}
