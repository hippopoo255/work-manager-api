<?php

namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlogCommentPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can create models.
   *
   * @param \App\Models\User $user
   * @return mixed
   */
  public function create(User $user)
  {
    return true;
  }

  /**
   * Determine whether the user can update the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\BlogComment $blogComment
   * @return mixed
   */
  public function update(User $user, BlogComment $blogComment)
  {
    return $blogComment->created_by === $user->id;
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\BlogComment $blogComment
   * @return mixed
   */
  public function delete(User $user, BlogComment $blogComment)
  {
    return $blogComment->created_by === $user->id ||
      $blogComment->blog->created_by === $user->id;
  }
}
