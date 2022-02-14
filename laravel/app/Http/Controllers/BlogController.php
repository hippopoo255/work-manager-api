<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Requests\Blog\StoreRequest;
use App\Http\Requests\Blog\UpdateRequest;
use App\Services\BlogService as Service;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
  /**
   * @var Service
   */
  private $service;
  private $perPage = 15;
  private $loads = ['createdBy', 'comments.createdBy', 'likes', 'images', 'tags'];


  public function __construct(Service $service)
  {
    $this->authorizeResource(Blog::class, 'id');
    $this->service = $service;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    return response($this->service->paginate($request->query(), $this->perPage, $this->loads), 200);
  }

  public function findByOwner()
  {
    return response($this->service->findByOwner($this->loads), 200);
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
      $blog = $this->service->store($request->all());
      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      throw $e;
    }
    return response($blog, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param Blog $id
   * @return \Illuminate\Http\Response
   */
  public function show(Blog $id)
  {
    return response($this->service->find($id), 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Blog $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateRequest $request, Blog $id)
  {
    DB::beginTransaction();
    try {
      $blog = $this->service->update($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      throw $e;
    }
    return response($blog, 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Blog $id
   * @return \Illuminate\Http\Response
   */
  public function like(Blog $id)
  {
    return response($this->service->like($id), 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Blog $id
   * @return \Illuminate\Http\Response
   */
  public function unlike(Blog $id)
  {
    return response($this->service->unlike($id), 204);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Blog $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Blog $id)
  {
    return response($this->service->delete($id), 204);
  }
}
