<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\User\ProfileRequest;
use App\Http\Requests\User\SettingRequest;
use App\Models\User;
use App\Services\NotifyValidationService;
use DB;

class UserController extends Controller
{

  private $service;
  private $notifyValidationService;

  public function __construct(
    UserService $service,
    NotifyValidationService $notifyValidationService
  ) {
    $this->service = $service;
    $this->notifyValidationService = $notifyValidationService;
  }

  public function index(Request $request)
  {
    return response($this->service->index($request->query()), 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param ProfileRequest $request
   * @param User $id
   * @return \Illuminate\Http\Response
   */
  public function updateProfile(ProfileRequest $request, User $id)
  {
    DB::beginTransaction();
    try {
      $user = $this->service->updateProfile($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      throw $e;
    }
    return response($user, 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param SettingRequest $request
   * @param User $id
   * @return \Illuminate\Http\Response
   */
  public function updateSetting(SettingRequest $request, User $id)
  {
    \DB::beginTransaction();
    try {
      $user = $this->service->updateSetting($request->all(), $id);
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollback();
      throw $e;
    }
    return response($user, 200);
  }

  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
   */
  public function currentUser()
  {
    $currentUser = $this->service->currentUser();
    return !!$currentUser ? response($currentUser) : response('');
  }

  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
   */
  public function withChatRooms()
  {
    $currentUser = $this->service->withChatRooms();
    return response($currentUser);
  }

  public function notifyValidationByUser(User $id)
  {
    return response($this->service->notifyStatus($id));
  }
}
