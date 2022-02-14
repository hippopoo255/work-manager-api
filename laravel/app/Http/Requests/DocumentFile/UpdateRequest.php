<?php

namespace App\Http\Requests\DocumentFile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends StoreRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return $this->user()->can('update', $this->route('id'));
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
      'original_name' => 'required|string|max:80',
      'is_public' => 'nullable|boolean',
      'sharedMembers' => 'nullable|array',
      'sharedMembers.*' => 'required|integer|' . Rule::in(User::pluck('id')->toArray()),
      'sharedMembers.*.is_editable' => 'required|boolean',
      'sharedMembers.*.shared_by' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
    ];
  }

  public function attributes()
  {
    return parent::attributes();
  }
}
