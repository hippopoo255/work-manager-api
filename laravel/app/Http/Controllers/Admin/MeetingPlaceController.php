<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MeetingPlace\StoreRequest;
use App\Http\Requests\MeetingPlace\UpdateRequest;
use App\Services\MeetingPlaceService as Service;
use App\Models\MeetingPlace;
use Illuminate\Database\QueryException;

class MeetingPlaceController extends Controller
{
  /**
   * @var Service;
   */
  private $service;

  public function __construct(Service $service)
  {
    $this->service = $service;
  }
  /**
   * @param Request $request
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    return response($this->service->all($request->query()), 200);
  }

  /**
   * @param StoreRequest $request
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   * @throws \Throwable
   */
  public function store(StoreRequest $request)
  {
    \DB::beginTransaction();
    try {
      $model = $this->service->store($request->all());
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();
      throw $e;
    }
    return response($model, 201);
  }

  /**
   * @param UpdateRequest $request
   * @param MeetingPlace $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   * @throws \Throwable
   */
  public function update(UpdateRequest $request, MeetingPlace $id)
  {
    \DB::beginTransaction();
    try {
      $model = $this->service->update($request->all(), $id);
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();
      throw $e;
    }
    return response($model, 200);
  }

  /**
   * Undocumented function
   *
   * @param Request $request
   * @param MeetingPlace $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   * @throws \Throwable
   */
  public function destroy(Request $request, MeetingPlace $id)
  {
    \DB::beginTransaction();
    try {
      $meetingPlace = $this->service->delete($id);
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollback();
      if ($e instanceof QueryException) {
        return response([
          'errors' => [
            'db' => ['関連付けられた議事録が存在するため削除できません。']
          ]
        ], 422);
      }
      throw $e;
    }
    // 204のためNo Content
    return response($meetingPlace, 204);
  }
}
