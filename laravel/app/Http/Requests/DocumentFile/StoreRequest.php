<?php

namespace App\Http\Requests\DocumentFile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
  const MULTIPLE_NUM = 5;
  const MAX_FILE_SIZE = 1024 * self::MULTIPLE_NUM; // 5M
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
      'is_public' => 'nullable|boolean',
      'file' => 'required|file|max:' . self::MAX_FILE_SIZE,
      'sharedMembers' => 'nullable|array',
      'sharedMembers.*.is_editable' => 'required|boolean',
      'sharedMembers.*.shared_by' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
    ];
  }

  public function attributes()
  {
    return [
      'created_by' => 'アップロード者のID',
      'file' => 'ファイル',
      'is_public' => '公開設定',
      'sharedMembers.*.is_editable' => '編集権限',
      'sharedMembers.*.shared_by' => '共有した人',
    ];
  }
}
