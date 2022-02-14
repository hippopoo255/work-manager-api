<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MeetingRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingRecordPolicy
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
    //
  }

  /**
   * Determine whether the user can view the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\MeetingRecord $meetingRecord
   * @return mixed
   */
  public function view(User $user, MeetingRecord $meetingRecord)
  {
    return $meetingRecord->role->value <= $user->role->value;
  }

  /**
   * Determine whether the user can create models.
   *
   * @param \App\Models\User $user
   * @return mixed
   */
  public function create(User $user)
  {
    //
  }

  /**
   * Determine whether the user can update the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\MeetingRecord $meetingRecord
   * @return mixed
   */
  public function update(User $user, MeetingRecord $meetingRecord)
  {
    return $user->id === $meetingRecord->created_by;
  }

  /**
   * Determine whether the user can delete the model.
   *
   * @param \App\Models\User $user
   * @param MeetingRecord $meetingRecord
   * @return mixed
   */
  public function delete(User $user, MeetingRecord $meetingRecord)
  {
    return $user->id === $meetingRecord->created_by;
  }

  /**
   * Determine whether the user can restore the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\MeetingRecord $meetingRecord
   * @return mixed
   */
  public function restore(User $user, MeetingRecord $meetingRecord)
  {
    //
  }

  /**
   * Determine whether the user can permanently delete the model.
   *
   * @param \App\Models\User $user
   * @param \App\Models\MeetingRecord $meetingRecord
   * @return mixed
   */
  public function forceDelete(User $user, MeetingRecord $meetingRecord)
  {
    //
  }
}
