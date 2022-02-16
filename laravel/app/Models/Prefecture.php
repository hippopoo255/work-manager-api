<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Prefecture
 *
 * @property int $id
 * @property string $name 都道府県名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture query()
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Prefecture whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Prefecture extends Model
{
    //
}
