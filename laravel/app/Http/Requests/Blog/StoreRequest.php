<?php

namespace App\Http\Requests\Blog;

use App\Models\Tag;
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
      'created_by' => 'nullable|integer|' . Rule::in(User::pluck('id')->toArray()),
      'title' => 'required|string|max:140',
      'body' => 'required|string',
      'images' => 'nullable|array',
      'images.*.file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:' . self::MAX_FILE_SIZE,
      'tags' => 'nullable|array',
      'tags.*' => 'required|integer|' . Rule::in(Tag::pluck('id')->toArray()),
    ];
  }

  public function attributes()
  {
    return [
      'created_by' => '投稿者のID',
      'title' => 'タイトル',
      'body' => '本文',
      'images.*.file' => '画像データ',
      'tags.*' => 'タグ',
    ];
  }

  public function messages()
  {
    return [
      'images.*.file.max' => '画像サイズは' . self::MULTIPLE_NUM . 'MB以下のものを指定してください',
      'tags.*.in' => 'タグの指定が不正な形式です',
    ];
  }
}
