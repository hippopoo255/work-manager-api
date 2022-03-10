<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use App\Contracts\Models\ModelInterface;
use App\Services\Supports\StrSupportTrait;

/**
 * App\Models\Activity
 *
 * @property int $id
 * @property int $user_id ユーザID
 * @property int $action_type_id アクションID
 * @property int $is_read 既読フラグ
 * @property string $content 通知内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereActionTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $model_id モデルID
 * @property-read \App\Models\ActionType|null $actionType
 * @property-read string $replaced_link
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereModelId($value)
 * @property-read \App\Models\User $user
 * @property int $created_by 誰のアクティビティか
 * @property-read \App\Models\User $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedBy($value)
 */
class Activity extends Model implements ModelInterface
{
  use StrSupportTrait;

  protected $table = 'activities';

  protected $fillable = [
    'action_type_id',
    'user_id',
    'model_id',
    'is_read',
    'content',
    'created_by',
  ];

  protected $appends = [
    'replaced_link',
  ];

  const RELATIONS_ARRAY = [
    'user'
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function actionType()
  {
    return $this->hasOne(ActionType::class, 'id', 'action_type_id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }

  /**
   * @return string
   */
  public function getReplacedLinkAttribute(): string
  {
    if (!is_null($this->model_id)) {
      return $this->replaceAttribute($this->actionType->link, ['id' => $this->model_id]);
    }
    return $this->actionType->link;
  }
}
