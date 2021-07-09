<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BlogLike
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BlogLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogLike query()
 * @mixin \Eloquent
 */
class BlogLike extends Model
{
  protected $table = 'likes';
}
