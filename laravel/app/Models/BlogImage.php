<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BlogImage
 *
 * @property int $id
 * @property int $blog_id ブログID
 * @property string $file_path 画像パス
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Blog $blog
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage newQuery()
 * @method static \Illuminate\Database\Query\Builder|BlogImage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage whereBlogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlogImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BlogImage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BlogImage withoutTrashed()
 * @mixin \Eloquent
 */
class BlogImage extends Model
{
  use SoftDeletes;

  protected $table = 'blog_images';

  protected $fillable = [
    'blog_id',
    'file_path',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function blog()
  {
    return $this->belongsTo(Blog::class, 'blog_id', 'id');
  }

}
