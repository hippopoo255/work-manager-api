<?php

namespace App\Http\Requests\MeetingRecord;

use App\Models\MeetingPlace;
use App\Models\Priority;
use App\Models\Progress;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'created_by' => 'required|integer|' . Rule::in(User::pluck('id')->toArray()),
      'place_id' => 'nullable|integer|' . Rule::in(MeetingPlace::pluck('id')->toArray()),
      'meeting_date' => 'required|date_format:Y/m/d H:i',
      'role_id' => 'nullable|integer|' . Rule::in(Role::pluck('id')->toArray()),
      //      'title' => 'required|string|max:80|regex:/^[ぁ-んァ-ヶー一-龠\s\w\$\(\)~\.=\+\-０-９、。（）「」\n\r]+$/u',
      'title' => 'required|string|max:80',
      'summary' => 'nullable|string',
      'members' => 'required|array',
      'members.*' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
      'meeting_decisions' => 'nullable|array',
      'meeting_decisions.*.decided_by' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
      'meeting_decisions.*.created_by' => 'required|integer|' . Rule::in(User::pluck('id')->toArray()),
      'meeting_decisions.*.subject' => 'nullable|string|max:80',
      'meeting_decisions.*.body' => 'required|string|max:140',
      'meeting_decisions.*.tasks' => 'nullable|array',
      'meeting_decisions.*.tasks.*.owner_id' => 'required|integer|' . Rule::in(User::pluck('id')->toArray()),
      'meeting_decisions.*.tasks.*.created_by' => 'required|integer|' . Rule::in(User::pluck('id')->toArray()),
      'meeting_decisions.*.tasks.*.priority_id' => 'nullable|integer|' . Rule::in(Priority::pluck('id')->toArray()),
      'meeting_decisions.*.tasks.*.progress_id' => 'nullable|integer|' . Rule::in(Progress::pluck('id')->toArray()),
      'meeting_decisions.*.tasks.*.body' => 'required|string|max:140|regex:/\A[ぁ-んァ-ヶー一-龠\s\w\$\(\)~\.=\+\-０-９、。（）「」\n\r]+\z/u',
      'meeting_decisions.*.tasks.*.time_limit' => 'required|date_format:Y/m/d H:i',
    ];
  }

  public function attributes()
  {
    return [
      'created_by' => '議事録作成者',
      'place_id' => '開催場所',
      'meeting_date' => '開催日時',
      'title' => '会議名',
      'summary' => '会議概要',
      'meeting_decisions.*.decided_by' => '決定者',
      'meeting_decisions.*.created_by' => '入力者',
      'meeting_decisions.*.subject' => '議題',
      'meeting_decisions.*.body' => '決定内容',
      'meeting_decisions.*.tasks.*.owner_id' => 'タスク担当者',
      'meeting_decisions.*.tasks.*.created_by' => 'タスク登録者',
      'meeting_decisions.*.tasks.*.priority_id' => '優先度',
      'meeting_decisions.*.tasks.*.progress_id' => '進捗度',
      'meeting_decisions.*.tasks.*.body' => 'タスクの内容',
      'meeting_decisions.*.tasks.*.time_limit' => 'タスクの期限',
    ];
  }
}
