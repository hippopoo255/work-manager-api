<?php

namespace App\Http\Requests\ChatMessage;

use App\Models\ChatMessageImage;
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
    return $this->user()->can('storeMessage', $this->route('id'));
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
      'mentioned_to' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
      'body' => 'required|string',
      'files' => 'nullable|array|max:4',
      'files.*' => [
        'nullable',
        'mimes:jpg,jpeg,png,gif,svg',
        'max:' . self::MAX_FILE_SIZE
      ],
    ];
  }

  public function attributes()
  {
    return [
      'created_by' => '投稿者',
      'mentioned_to' => '送信相手',
      'body' => 'メッセージ',
      'files' => '画像',
      'files.*' => '画像',
    ];
  }

  public function messages()
  {
    return [
      'created_by.in' => '投稿者のデータが正しくありません',
      'mentioned_to' => '送信相手のデータが正しくありません',
      'files.*.mimes' => ':attributeの形式が正しくありませんでした'
    ];
  }
}
