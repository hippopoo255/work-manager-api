<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Priority;
use App\Models\Progress;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
class TaskFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $taskList = [
      '資料作成',
      '資料作成',
      '資料作成',
      '資料作成',
      '資料作成',
      'ミーティング資料作成',
      'ミーティング資料作成',
      'ミーティング資料作成',
      'ミーティング資料作成',
      'ミーティング資料作成',
      '研修資料の作成',
      '研修資料の作成',
      '新入社員用AWSアカウント作成',
      '新入社員用AWSアカウント作成',
      'プレゼン資料作成',
      '集計',
      'トップページのUI実装',
      'Terraformでインフラをコード化する',
      '新サービスのプロトタイプをAmplifyにデプロイする',
      'Redmineをherokuにデプロイする',
      'ステージング環境を構築する',
      '認証データを状態管理ツールでグローバル化する',
      '新しい本番環境のアーキテクチャ設計',
      '追加機能の要件定義',
    ];

    return [
      'meeting_decision_id' => null,
      'owner_id' => $this->faker->randomNumber,
      'created_by' => $this->faker->randomNumber,
      'priority_id' => array_random(Priority::all()->pluck('id')->toArray()),
      'progress_id' => array_random(Progress::all()->pluck('id')->toArray()),
      'body' => array_random($taskList),
      'time_limit' => $this->faker->dateTimeThisMonth,
    ];
  }
}
