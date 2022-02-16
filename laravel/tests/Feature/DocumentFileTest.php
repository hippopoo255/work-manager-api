<?php

namespace Tests\Feature;

use App\Models\DocumentFile;
use App\Models\DocumentFolder;
use App\Models\DocumentShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentFileTest extends TestCase
{

  protected $documentFolder;
  protected $members;
  protected $membersData;

  protected function setUp(): void
  {
    parent::setUp();
    $this->documentFolder = factory(DocumentFolder::class)->create([
      'created_by' => $this->user->id
    ]);
    $this->members = User::where('id', '!=', $this->user->id)
      ->get()
      ->shuffle()
      ->splice(0, random_int(1, 15))
      ->all();
    foreach ($this->members as $member) {
      // 編集権限があるメンバー
      $this->membersData[$member->id] = [
        'is_editable' => 1,
        'shared_by' => $this->user->id
      ];
    }
    // 編集権限のないメンバー
    $this->membersData[$this->user->id] = [
      'is_editable' => 0,
      'shared_by' => $this->user->id
    ];
  }

  /**
   * @test
   * @group document_file
   */
  public function should_ドキュメントファイルの投稿()
  {
    Storage::fake('s3');
    $expects = [
      'created_by' => $this->members[0]['id'],
      'file' => UploadedFile::fake()->image('test.png'),
      'sharedMembers' => $this->membersData,
    ];
    $response = $this->actingAs($this->members[0])->postJson(route('documentFile.store', $this->documentFolder), $expects);
    $new = DocumentFile::orderBy('id', 'desc')->first();
    $response->assertCreated()->assertJson([
      'id' => $new->id
    ]);
    Storage::disk('s3')->assertExists($new->file_path);

    $result = parent::$openApiValidator->validate('postDocumentFile', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_投稿時のバリデーションエラー()
  {
    // 必須項目"file"と"created_by"がない
    $expects = [
      'sharedMembers' => [
        [0 => 'invalid data'],/* 必須項目"is_editable"がない */
      ]
    ];
    $response = $this->actingAs($this->members[0])->postJson(route('documentFile.store', $this->documentFolder), $expects);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'file', 'created_by', 'sharedMembers.0.is_editable'
    ]);
    $result = parent::$openApiValidator->validate('postDocumentFile', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_共有者以外は限定公開のファイル閲覧禁止()
  {
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $badUser = User::whereNotIn('id', array_keys($this->membersData))->get()->first();
    $response = $this->actingAs($badUser)->getJson(route('documentFile.show', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]));
    $response->assertForbidden();

    $result = parent::$openApiValidator->validate('getDocumentFileId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_ファイル閲覧()
  {
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $response = $this->actingAs($this->user)->getJson(route('documentFile.show', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]));
    $response->assertOk()->assertJson([
      'id' => $file->id,
    ]);

    $result = parent::$openApiValidator->validate('getDocumentFileId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_共有者以外の更新禁止()
  {
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $badUser = User::whereNotIn('id', array_keys($this->membersData))->get()->first();
    $willDenied = [
      'original_name' => $file->original_name . '_update',
      'created_by' => $badUser->id,
    ];
    $response = $this->actingAs($badUser)->putJson(route('documentFile.update', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]), $willDenied);
    $response->assertForbidden();

    $result = parent::$openApiValidator->validate('putDocumentFileId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_共有者でも編集権限がないメンバーによる更新禁止()
  {
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $willDenied = [
      'original_name' => $file->original_name . '_update',
      'created_by' => $this->user->id,
    ];
    $response = $this->actingAs($this->user)->putJson(route('documentFile.update', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]), $willDenied);
    $response->assertForbidden();

    $result = parent::$openApiValidator->validate('putDocumentFileId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_編集権限を持つメンバーによる更新()
  {
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $expects = [
      'original_name' => $file->original_name . '_update',
      'created_by' => $file->created_by,
    ];
    $response = $this->actingAs($this->members[0])->putJson(route('documentFile.update', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]), $expects);
    $response->assertOk()->assertJson([
      'id' => $file->id,
      'original_name' => $expects['original_name'],
    ]);

    $result = parent::$openApiValidator->validate('putDocumentFileId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group document_file
   */
  public function should_編集権限のないメンバーによる削除禁止()
  {
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $count = DocumentShare::count();
    $response = $this->actingAs($this->user)->deleteJson(route('documentFile.destroy', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]));
    $response->assertForbidden();
    $this->assertDatabaseHas('document_files', [
      'id' => $file->id,
      'deleted_at' => null,
    ])->assertDatabaseCount('document_shares', $count);

    $result = parent::$openApiValidator->validate('deleteDocumentFileId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
  /**
   * @test
   * @group document_file
   */
  public function should_編集権限を持つメンバーによる削除()
  {
    Storage::fake('s3');
    $file = factory(DocumentFile::class)->create([
      'created_by' => $this->members[0],
    ]);
    $file->sharedMembers()->sync($this->membersData);
    $response = $this->actingAs($this->members[0])->deleteJson(route('documentFile.destroy', [
      'id' => $this->documentFolder->id,
      'document_file_id' => $file->id,
    ]));
    $response->assertNoContent();
    $this->assertSoftDeleted('document_files', [
      'id' => $file->id
    ]);

    $result = parent::$openApiValidator->validate('deleteDocumentFileId', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
