<?php

namespace App\Models;

use App\Models\Abstracts\CommonModel as Model;
use App\Contracts\Models\ModelInterface;

/**
 * App\Models\ActionType
 *
 * @property int $id
 * @property string $key アクションキー
 * @property string $label_name アクション名
 * @property string $template_message テンプレートメッセージ
 * @property string $link リンク
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereLabelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereTemplateMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_notify ユーザが通知設定の画面で変更できるか
 * @property int $is_admin 管理者フラグ
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActionType whereIsNotify($value)
 */
class ActionType extends Model implements ModelInterface
{
  protected $table = 'action_types';

  protected $fillable = [
    'name',
    'template_message',
    'link',
    'is_admin',
  ];

  const SCHEDULE_SHARED_KEY = 'schedule_shared';
  const MESSAGE_SENT_KEY = 'message_sent';
  const MEETING_RECORD_JOINED_KEY = 'meeting_record_joined';
  const USER_CREATE_KEY = 'user_create';
  const ADMIN_CREATE_KEY = 'admin_create';
  const DEPARTMENT_CREATE_KEY = 'department_create';
  const PROGRESS_CREATE_KEY = 'progress_create';
  const MEETING_PLACE_CREATE_KEY = 'meeting_place_create';
  const BLOG_REPORT_KEY = 'blog_report';
  const CHAT_REPORT_KEY = 'chat_report';

  const LIMIT_TASK_KEY = 'daily_limit_task';
  const LIMIT_SCHEDULE_KEY = 'daily_schedule';

  const DAILY_ALERT_ARR = [
    self::LIMIT_TASK_KEY => 4,
    self::LIMIT_SCHEDULE_KEY => 5,
  ];

  const ADMIN_ACTION_TYPES = [
    self::USER_CREATE_KEY,
    self::ADMIN_CREATE_KEY,
    self::DEPARTMENT_CREATE_KEY,
    self::PROGRESS_CREATE_KEY,
    self::MEETING_PLACE_CREATE_KEY,
  ];

  const USER_ACTION_TYPES = [
    self::SCHEDULE_SHARED_KEY,
    self::MESSAGE_SENT_KEY,
    self::MEETING_RECORD_JOINED_KEY,
    self::BLOG_REPORT_KEY,
    self::CHAT_REPORT_KEY,
  ];
}
