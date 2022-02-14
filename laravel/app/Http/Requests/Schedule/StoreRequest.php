<?php

namespace App\Http\Requests\Schedule;

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
      'title' => 'required|string|max:140',
      'start' => 'required|date_format:Y/m/d H:i',
      'end' => 'required|date_format:Y/m/d H:i',
      'is_public' => 'nullable|boolean',
      'color' => 'nullable|string|max:50|regex:/^#[0-9a-fA-F]{3,6}$/u',
      'memo' => 'nullable|string',
      'sharedMembers' => 'nullable|array',
      'sharedMembers.*' => 'nullable|array',
      'sharedMembers.*.is_editable' => 'required|boolean',
      'sharedMembers.*.shared_by' => 'required|integer|' . Rule::in(User::pluck('id')->toArray()),
    ];
  }

  public function attributes()
  {
    return [
      'created_by' => '予定作成者のID',
      'title' => '内容',
      'start' => '予定開始日',
      'end' => '予定終了日',
      'is_public' => '公開設定',
      'color' => 'カラー',
      'memo' => 'メモ',
      'sharedMembers.*.id_editable' => '編集権限',
      'sharedMembers.*.shared_by' => '共有した人',
    ];
  }
}
