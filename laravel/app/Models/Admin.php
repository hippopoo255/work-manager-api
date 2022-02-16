<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $login_id ログインID
 * @property string $family_name 姓
 * @property string $given_name 名
 * @property string|null $family_name_kana セイ
 * @property string|null $given_name_kana メイ
 * @property string|null $file_path 画像
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $created_by 登録者
 * @property string|null $updated_by 更新者
 * @property string|null $deleted_by 削除者
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Query\Builder|Admin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFirstNameKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereLastNameKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereLoginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|Admin withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Admin withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFamilyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereFamilyNameKana($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereGivenName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereGivenNameKana($value)
 * @property int|null $admin_code ユーザーコード
 * @property int $role_id ロールID
 * @property string $cognito_sub unique id of cognito admin
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereAdminCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCognitoSub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereRoleId($value)
 * @property string|null $department_code 部署ID
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereDepartmentCode($value)
 * @property int|null $organization_id 会社ID
 * @property-read \App\Models\Organization|null $organization
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereOrganizationId($value)
 */
class Admin extends Authenticatable
{
  use SoftDeletes;
  use Notifiable;

  protected $table = 'admins';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'family_name',
    'given_name',
    'family_name_kana',
    'given_name_kana',
    'file_path',
    'email',
    'admin_code',
    'role_id',
    'login_id',
    'password',
    'created_by',
    'updated_by',
    'deleted_by',
    'cognito_sub',
    'department_code',
    'organization_id',
  ];

  protected $appends = [
    'full_name',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'email_verified_at',
    'login_id',
    'cognito_sub',
    'role_id',
    'deleted_at',
    'created_by',
    'updated_by',
    'deleted_by',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * @return string
   */
  public function getFullNameAttribute()
  {
    return $this->family_name . ' ' . $this->given_name;
  }

  /**
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function organization()
  {
    return $this->belongsTo(Organization::class, 'organization_id', 'id');
  }
}
