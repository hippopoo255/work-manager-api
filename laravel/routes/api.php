<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function () {
  Route::get('/', function () {
    return 'Hello';
  });
  // 認証中の一般ユーザーを返却
  Route::get('/user/current', 'UserController@currentUser')->name('currentUser');
  Route::get('/user/current/chat_rooms', 'UserController@withChatRooms')->name('withChatRooms');
  Route::get('/meeting_place', 'MeetingPlaceController@index')->name('meetingPlace.index');
  // ユーザ（候補一覧・メール重複チェック）
  Route::get('/user', 'UserController@index')->name('user.index');

  // 認証手続
  Route::namespace('Auth')->group(function () {
    // Route::post('/register', 'RegisterController@register')->name('register');
    // Route::post('/login', 'LoginController@login')->name('login');
    // Route::post('/testlogin', 'LoginController@testLogin')->middleware('rewriteuser')->name('testlogin');
    // パスワードリセット
    // Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    // Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');

    // Route::middleware('auth')->group(function () {
    // Route::post('/logout', 'LoginController@logout')->name('logout');
    // });
  });

  // 一般認証が必要なAPI
  Route::middleware('auth')->group(function () {
    // 組織情報の登録・更新
    Route::post('/organization', 'OrganizationController@store')->name('organization.store');
    Route::put('/organization/{id}', 'OrganizationController@update')->name('organization.update');
    Route::get('/prefecture', 'PrefectureController@index')->name('prefecture.index');

    // 組織情報の登録後にアクセス可能なエンドポイント
    Route::middleware('org.exists')->group(function () {
      Route::get('/organization/{id}', 'OrganizationController@show')->name('organization.show');
      // 優先順位
      Route::get('/priority', 'PriorityController@index')->name('priority.index');
      // 進捗状態
      Route::get('/progress', 'ProgressController@index')->name('progress.index');
      // スケジュール
      Route::get('/schedule', 'ScheduleController@index')->name('schedule.index');

      // リソースの属する組織とリクエストユーザの組織が一致している場合にアクセス可能なエンドポイント
      Route::middleware('org.match')->group(function () {
        // プロフィール
        Route::put('/user/{id}/profile', 'UserController@updateProfile')->name('user.update');
        // 設定
        Route::put('/user/{id}/setting', 'UserController@updateSetting')->name('user.setting');
        Route::get('/user/{id}/notify_validation', 'UserController@notifyValidationByUser')->name('user.notifyValidationByUser');
        // アクティビティ
        Route::put('/user/{id}/activity/read', 'ActivityController@read')->name('activity.read');
        Route::get('/user/{id}/activity', 'ActivityController@findByUser')->name('activity.findByUser');

        // スケジュール
        Route::get('/user/{id}/schedule', 'ScheduleController@findByOwner')->name('schedule.findByOwner');
        Route::post('/schedule', 'ScheduleController@store')->name('schedule.store');
        Route::put('/schedule/{id}', 'ScheduleController@update')->name('schedule.update');
        Route::get('/schedule/{id}', 'ScheduleController@show')->name('schedule.show');
        Route::delete('/schedule/{id}', 'ScheduleController@destroy')->name('schedule.destroy');
        Route::get('/author/schedule/daily', 'ScheduleController@dailyByAuthor')->name('schedule.dailyByAuthor');

        // チャットルーム
        Route::get('/author/chat_room', 'ChatRoomController@findByOwner')->name('chatRoom.findByOwner');
        Route::post('/chat_room', 'ChatRoomController@store')->name('chatRoom.store');
        Route::put('/chat_room/{id}', 'ChatRoomController@update')->name('chatRoom.update');
        Route::get('/chat_room/{id}', 'ChatRoomController@show')->name('chatRoom.show');
        Route::delete('/chat_room/{id}', 'ChatRoomController@destroy')->name('chatRoom.destroy');
        // チャットメッセージ
        Route::post('/chat_room/{id}/message', 'ChatMessageController@store')->name('chatMessage.store');
        Route::put('/chat_room/{id}/message/{chat_message_id}', 'ChatMessageController@update')->name('chatMessage.update');
        Route::delete('/chat_room/{id}/message/{chat_message_id}', 'ChatMessageController@destroy')->name('chatMessage.destroy');
        // 既読
        Route::post('/chat_room/{id}/read', 'ChatMessageReadController@store')->name('chatMessageRead.store');
        Route::get('/author/chat_message/unread/recently', 'ChatMessageReadController@unreadRecently')->name('chatMessageRead.unreadRecently');

        // ドキュメントフォルダ
        Route::get('/document_folder', 'DocumentFolderController@index')->name('documentFolder.index');
        Route::post('/document_folder', 'DocumentFolderController@store')->name('documentFolder.store');
        Route::put('/document_folder/{id}', 'DocumentFolderController@update')->name('documentFolder.update');
        Route::delete('/document_folder/{id}', 'DocumentFolderController@destroy')->name('documentFolder.destroy');
        // ドキュメントファイル
        Route::get('/document_folder/{id}/document_file', 'DocumentFileController@index')->name('documentFile.index');
        Route::post('/document_folder/{id}/document_file', 'DocumentFileController@store')->name('documentFile.store');
        Route::get('/document_folder/{id}/document_file/{document_file_id}', 'DocumentFileController@show')->name('documentFile.show');
        Route::put('/document_folder/{id}/document_file/{document_file_id}', 'DocumentFileController@update')->name('documentFile.update');
        Route::delete('/document_folder/{id}/document_file/{document_file_id}', 'DocumentFileController@destroy')->name('documentFile.destroy');

        // 会議議事録
        Route::get('/meeting_record', 'MeetingRecordController@index')->name('meetingRecord.index');
        Route::post('/meeting_record', 'MeetingRecordController@store')->name('meetingRecord.store');
        Route::get('/meeting_record/{id}', 'MeetingRecordController@show')->name('meetingRecord.show');
        Route::put('/meeting_record/{id}', 'MeetingRecordController@update')->name('meetingRecord.update');
        Route::delete('/meeting_record/{id}', 'MeetingRecordController@destroy')->name('meetingRecord.destroy');
        Route::get('/author/meeting_record/recently', 'MeetingRecordController@recently')->name('meetingRecord.recently');
        // 議事録のブックマーク
        Route::post('/meeting_record/{id}/bookmark', 'MeetingRecordPinController@bookmark')->name('meetingRecordPin.bookmark');
        Route::put('/meeting_record/{id}/bookmark', 'MeetingRecordPinController@unbookmark')->name('meetingRecordPin.unbookmark');

        // タスク
        Route::get('/author/task', 'TaskController@findByOwner')->name('task.findByOwner')->middleware('org.filter');
        Route::delete('/author/task', 'TaskController@deleteAll')->name('task.deleteAll');
        Route::get('/author/task/busy', 'TaskController@busyTaskByAuthor')->name('task.busyTaskByAuthor');
        Route::get('/task', 'TaskController@index')->name('task.index');
        Route::post('/task', 'TaskController@store')->name('task.store');
        Route::put('/task/{id}', 'TaskController@update')->name('task.update');
        Route::delete('/task/{id}', 'TaskController@destroy')->name('task.destroy');

        // ブログ
        Route::get('/author/blog', 'BlogController@findByOwner')->name('blog.findByOwner');
        Route::get('/blog', 'BlogController@index')->name('blog.index');
        Route::post('/blog', 'BlogController@store')->name('blog.store');
        Route::get('/blog/{id}', 'BlogController@show')->name('blog.show');
        Route::put('/blog/{id}', 'BlogController@update')->name('blog.update');
        Route::put('/blog/{id}/like', 'BlogController@like')->name('blog.like');
        Route::delete('/blog/{id}/like', 'BlogController@unlike')->name('blog.unlike');
        Route::delete('/blog/{id}', 'BlogController@destroy')->name('blog.destroy');
        // ブログコメント
        Route::post('/blog/{id}/comment', 'BlogCommentController@store')->name('blogComment.store');
        Route::put('/blog/{id}/comment/{comment_id}', 'BlogCommentController@update')->name('blogComment.update');
        Route::delete('/blog/{id}/comment/{comment_id}', 'BlogCommentController@destroy')->name('blogComment.destroy');
      });

      Route::middleware('org.match:id,user_id')->group(function () {
        // アクティビティ
        Route::put('/activity/{id}', 'ActivityController@update')->name('activity.update');
      });
    });
  });
});
