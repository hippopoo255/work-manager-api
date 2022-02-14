<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BlogComment
 *
 * @property int $id
 * @property int $created_by 投稿者
 * @property int $blog_id ブログID
 * @property string $body コメント
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Blog $blog
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|BlogComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereBlogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogComment whereCreatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|BlogComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BlogComment withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User $createdBy
 */
class BlogComment extends Model
{
  use SoftDeletes;

  protected $table = 'blog_comments';

  protected $fillable = [
    'created_by',
    'blog_id',
    'body',
  ];

  const RELATIONS_ARRAY = [
    'createdBy',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function blog()
  {
    return $this->belongsTo(Blog::class, 'blog_id', 'id');
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by', 'id');
  }
}
