<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogComment\StoreRequest;
use App\Http\Requests\BlogComment\UpdateRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Models\BlogComment;
use App\Services\BlogCommentService as Service;
use Illuminate\Support\Facades\DB;

class BlogCommentController extends Controller
{
  /**
   * @var Service
   */
  private $service;

  public function __construct(Service $service)
  {
    $this->authorizeResource(BlogComment::class, 'comment_id');
    $this->service = $service;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param StoreRequest $request
   * @param Blog $id
   * @return \Illuminate\Http\Response
   */
  public function store(StoreRequest $request, Blog $id)
  {
    DB::beginTransaction();
    try {
      $blogComment = $this->service->store($request->all(), $id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($blogComment, 201);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UpdateRequest $request
   * @param Blog $id
   * @param BlogComment $comment_id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateRequest $request, Blog $id, BlogComment $comment_id)
  {
    DB::beginTransaction();
    try {
      $blogComment = $this->service->update($request->all(), $comment_id);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return response($blogComment, 200);
  }

  /**
   * @param Blog $id
   * @param BlogComment $comment_id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Blog $id, BlogComment $comment_id)
  {
    return response($this->service->delete($comment_id), 204);
  }
}
