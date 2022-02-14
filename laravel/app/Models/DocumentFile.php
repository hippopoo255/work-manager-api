<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * App\Models\DocumentFile
 *
 * @property int $id
 * @property int $created_by アップロード者
 * @property int $folder_id フォルダID
 * @property string $file_path ファイルパス
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\DocumentFolder $folder
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $sharedMembers
 * @property-read int|null $shared_members_count
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile newQuery()
 * @method static \Illuminate\Database\Query\Builder|DocumentFile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|DocumentFile withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DocumentFile withoutTrashed()
 * @mixin \Eloquent
 * @property string $is_public 公開設定
 * @property string $original_name オリジナルファイル名
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentFile whereOriginalName($value)
 */
class DocumentFile extends Model
{
  use SoftDeletes;

  protected $table = 'document_files';
  protected $fillable = [
    'created_by',
    'folder_id',
    'file_path',
    'original_name',
  ];

  const RELATIONS_ARRAY = [
    'createdBy', 'sharedMembers'
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function folder()
  {
    return $this->belongsTo(DocumentFolder::class, 'folder_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function sharedMembers()
  {
    return $this->belongsToMany(User::class, 'document_shares', 'file_id', 'shared_with')
      ->as('option')->withTimestamps()->withPivot('shared_by', 'is_editable');
  }

  /**
   * @param $fileName
   */
  public function setPublicName($fileName)
  {
    $this->attributes['public_name'] = \Crypt::decrypt($fileName);
  }
}
