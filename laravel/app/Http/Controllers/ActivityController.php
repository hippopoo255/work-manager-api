<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivityService;
use App\Models\User;
use App\Models\Activity;

class ActivityController extends Controller
{
  /**
   * @var ActivityService
   */
  private $service;

  public function __construct(ActivityService $service)
  {
    $this->service = $service;
  }

  /**
   * @param Request $request
   * @param User $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function read(Request $request, User $id)
  {
    $this->authorize('update', $id);
    \DB::beginTransaction();
    try {
      $res = $this->service->read($id);
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollback();
      throw $e;
    }
    return response($res, 200);
  }

  /**
   * @param User $user
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function findByUser(User $id)
  {
    return response($this->service->findByUser($id->id));
  }
}
