<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DocumentShare
 *
 * @property int $id
 * @property int $shared_with 共有相手
 * @property int $shared_by 共有者
 * @property int $file_id ファイルID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $sharedBy
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare query()
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare whereSharedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare whereSharedWith($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DocumentShare whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DocumentShare extends Model
{
  protected $table = 'document_shares';
  protected $fillable = [
    'shared_by',
  ];
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function sharedBy()
  {
    return $this->belongsTo(User::class, 'shared_by', 'id');
  }

}
