<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Prefecture;
use App\Models\User;

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
      'name' => ['required', 'string', ''],
      'name_kana' => ['required', 'regex:/\A[ァ-ヴーｦ-ﾟ]+\z/u'],
      'postal_code' => ['required', 'postalcode'],
      'pref_id' => 'required|' . Rule::in(Prefecture::pluck('id')->toArray()),
      'city' => ['required', 'string', 'max:128'],
      'address' => ['required', 'string', 'max:255'],
      'tel' => ['required', 'tel'],
    ];
  }

  public function attributes()
  {
    return [
      'name' => '団体名',
      'name_kana' => '団体名カナ',
      // 'pref_id' => '都道府県',
      // 'city' => '市区町村',
      'address' => '住所',
      // 'tel' => '電話番号',
      // 'supervisor_id' => '責任者',
    ];
  }
}
