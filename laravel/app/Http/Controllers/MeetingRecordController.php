<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingRecord\DeleteRequest;
use App\Models\MeetingRecord;
use Illuminate\Http\Request;
use App\Http\Requests\MeetingRecord\StoreRequest;
use App\Http\Requests\MeetingRecord\UpdateRequest;
use App\Services\MeetingRecordService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MeetingRecordController extends Controller
{
  private $perPage = 10;
  private $relation = ['createdBy', 'place', 'decisions', 'members'];
  private $service;

  public function __construct(MeetingRecordService $service)
  {
    $this->service = $service;
  }
  /**
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    return response($this->service->paginate($request->query(), $this->perPage));
  }

  /**
   * @param StoreRequest $request
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   * @throws \Throwable
   */
  public function store(StoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $meetingRecord = $this->service->store($request->all());
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($meetingRecord, 201);
  }

  /**
   * @param MeetingRecord $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function show(MeetingRecord $id)
  {
    $this->authorize('view', $id);
    return response($this->service->find($id), 200);
  }

  /**
   * @param UpdateRequest $request
   * @param MeetingRecord $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   * @throws \Throwable
   */
  public function update(UpdateRequest $request, MeetingRecord $id)
  {
    DB::beginTransaction();
    try {
      $meetingRecord = $this->service->update($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($meetingRecord, 200);
  }

  /**
   * @param DeleteRequest $request
   * @param MeetingRecord $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function destroy(DeleteRequest $request, MeetingRecord $id)
  {
    return response($this->service->delete($id, $request->query(), $this->perPage), 200);
  }

  public function recently()
  {
    return response($this->service->findByUser());
  }

  /**
   * @return \Illuminate\Http\Response
   */
  public function ids()
  {
    return response($this->service->ids(), 200);
  }
}
