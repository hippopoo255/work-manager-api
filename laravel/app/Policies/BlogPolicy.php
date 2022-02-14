<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BlogPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view any models.
   *
   * @param \App\Models\User $user
   * @return mixed
   */
  public function viewAny(User $user)
  {
    return true;
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\Blog $blog
   * @return mixed
   */
  public function view(User $user, Blog $blog)
  {
    return true;
  }

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
   * @param \App\Models\Blog $blog
   * @return mixed
   */
  public function update(User $user, Blog $blog)
  {
    return $blog->created_by === $user->id;
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\Blog $blog
   * @return mixed
   */
  public function delete(User $user, Blog $blog)
  {
    return $blog->created_by === $user->id;
  }
}
