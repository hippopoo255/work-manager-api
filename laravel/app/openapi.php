<?php

namespace App;
/**
 * @OA\Info(
 *  title="業務支援システム",
 *  description="主な機能：
   議事録の記録
   スケジュール管理・共有
   ファイル共有
   チャット
   Todo管理等",
   version="1.0.0",
 * )
 *
 * @OA\Server(
 *  description="OpenApi host",
 *  url="http://localhost:8080"
 * )
 *
 * @OA\Tag(
 *  name="user",
 *  description="user",
 * )
 */

/**
 * @OA\Schema(
 *   schema="user",
 *   type="object",
 *   description="ユーザー",
 *   required={"id","full_name","created_at","updated_at"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     description="ユーザーID",
 *   ),
 *   @OA\Property(
 *     property="full_name",
 *     type="string",
 *     example="テスト 太郎",
 *     description="フルネーム"
 *   ),
 *   @OA\Property(
 *     property="first_name",
 *     type="string",
 *     example="太郎",
 *     description="名"
 *   ),
 *   @OA\Property(
 *     property="last_name",
 *     type="string",
 *     example="テスト",
 *     description="姓"
 *   ),
 *   @OA\Property(
 *     property="file_path",
 *     type="string",
 *     example="users/test1.jpg",
 *     description="ファイルパス"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="datetime",
 *     example="2021-12-31 12:00:00",
 *     description="作成日時"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="datetime",
 *     example="2021-12-31 12:00:00",
 *     description="更新日時"
 *   )
 * )
 */

/**
 * @OA\Schema(
 *   title="User",
 *   description="ユーザーのスキーマ",
 *   type="object",
 *   required={"id","full_name","created_at","updated_at"},
 * )
 */
class User
{
  /**
   * ユーザID
   * @var integer
   * @OA\Property(
   *   example=1,
   * )
   */
  public $id;

  /**
   * ユーザーフルネーム
   * @var string
   * @OA\Property(
   *   example="テスト 太郎",
   * )
   */
  public $full_name;

  /**
   * 名
   * @var string
   * @OA\Property(
   *   example="太郎",
   * )
   */
  public $first_name;

  /**
   * 姓
   * @var string
   * @OA\Property(
   *   example="テスト",
   * )
   */
  public $last_name;

  /**
   * 画像パス
   * @var string
   * @OA\Property(
   *   example="user/user.jpg",
   * )
   */
  public $file_path;

  /**
   * 作成日時
   * @var string
   * @OA\Property(
   *   example="2021-12-31 23:59:59",
   * )
   */
  public $created_at;

  /**
   * 更新日時
   * @var string
   * @OA\Property(
   *   example="2021-12-31 23:59:59",
   * )
   */
  public $updated_at;
}
