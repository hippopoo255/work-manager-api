<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Models\Organization
 *
 * @property int $id
 * @property string|null $name 会社名
 * @property string|null $name_kana 会社名カナ
 * @property int|null $postal_code
 * @property int|null $pref_id 都道府県ID
 * @property string|null $city 市区町村
 * @property string|null $address 所在場所
 * @property string|null $tel 電話番号
 * @property int|null $supervisor_id 責任者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereNameKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization wherePrefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereSupervisorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Organization extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'name',
    'name_kana',
    'postal_code',
    'pref_id',
    'city',
    'address',
    'tel',
    'supervisor_id',
  ];

  protected $hidden = [
    'updated_at',
    'deleted_at'
  ];

  protected $appends = [
    'pref_name',
  ];

  /**
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function supervisor()
  {
    return $this->hasOne(User::class, 'id', 'supervisor_id');
  }
  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function prefecture()
  {
    return $this->belongsTo(Prefecture::class, 'pref_id', 'id');
  }

  /**
   * @return string
   */
  public function getPrefNameAttribute(): string
  {
    return $this->prefecture->name;
  }
}
