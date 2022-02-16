<?php

namespace App\Http\Requests\ChatMessage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends StoreRequest
{
  private $chatMessage;
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    $this->chatMessage = $this->route('chat_message_id');
    return $this->user()->can('update', $this->route('chat_message_id'));
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return array_merge(parent::rules(), [
      'delete_flags' => [
        'nullable',
        'array',
        'max:5',
      ],
      'delete_flags.*' => [
        'nullable',
        'integer',
        Rule::in($this->chatMessage->images->pluck('id')->toArray()),
      ],
    ]);
  }

  public function attributes()
  {
    return array_merge(parent::attributes(), [
      'delete_flags' => '削除フラグ',
      'delete_flags.*' => '削除フラグ',
    ]);
  }

  public function messages()
  {
    return array_merge(parent::messages(), [
      'delete_flags.*.in' => '削除フラグのデータが正しくありません',
    ]);
  }
}
