<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Authenticatable;

use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view any models.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function viewAny(User $user)
  {
    //
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Organization  $organization
   * @return mixed
   */
  public function view(User $user, Organization $organization)
  {
    $organization->id === $user->organization_id;
  }

  /**
   * Determine whether the user can create models.
   *
   * @param  \App\Models\User  $user
   * @return mixed
   */
  public function create(User $user)
  {
    return $user;
  }

  /**
   * Determine whether the user can update the model.
   *
   * @param  $user
   * @param  \App\Models\Organization  $organization
   * @return mixed
   */
  public function update($user, Organization $organization)
  {
    if ($user instanceof Admin) {
      return $organization->supervisor_id === $user->bUser->id;
    } else {
      return $organization->supervisor_id === $user->id;
    }
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Organization  $organization
   * @return mixed
   */
  public function delete(User $user, Organization $organization)
  {
    //
  }

  /**
   * Determine whether the user can restore the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Organization  $organization
   * @return mixed
   */
  public function restore(User $user, Organization $organization)
  {
    //
  }

  /**
   * Determine whether the user can permanently delete the model.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Organization  $organization
   * @return mixed
   */
  public function forceDelete(User $user, Organization $organization)
  {
    //
  }
}
