<?php

namespace App\Http\Requests\BlogComment;

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
      'body' => 'required|string|max:140',
      'created_by' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
    ];
  }

  public function attributes()
  {
    return [
      'body' => 'コメント',
      'created_by' => 'コメント投稿者ID',
    ];
  }
}
