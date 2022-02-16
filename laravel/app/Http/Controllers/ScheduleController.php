<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Schedule\StoreRequest;
use App\Http\Requests\Schedule\UpdateRequest;
use App\Services\ScheduleService;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
  /**
   * @var ScheduleService
   */
  protected $service;

  public function __construct(ScheduleService $service)
  {
    $this->authorizeResource(Schedule::class, 'id');
    $this->service = $service;
  }

  /**
   * Display a listing of the resource.
   * @param Request $request
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    return response($this->service->index($request->query()), 200);
  }

  /**
   * Display a listing of the resource.
   * @return \Illuminate\Http\Response
   */
  public function dailyByAuthor()
  {
    return response($this->service->index([
      'sharedMembers:shared_with' => Auth::user()->id,
      'sort_key' => 'start',
      'order_by' => 'asc',
    ]));
  }

  /**
   * @param Request $request
   * @param User $id
   */
  public function findByOwner(Request $request, User $id)
  {
    return response($this->service->findByOwner($id), 200);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param StoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $schedule = $this->service->store($request->all());
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($schedule, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param Schedule $id
   * @return \Illuminate\Http\Response
   */
  public function show(Schedule $id)
  {
    return response($this->service->find($id), 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Schedule $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateRequest $request, Schedule $id)
  {
    DB::beginTransaction();
    try {
      $schedule = $this->service->update($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($schedule, 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Schedule $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Schedule $id)
  {
    return response($this->service->delete($id), 204);
  }
}
