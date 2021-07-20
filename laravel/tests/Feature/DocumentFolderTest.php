<?php

namespace Tests\Feature;

use App\Models\DocumentFolder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class DocumentFolderTest extends TestCase
{
  /**
   * @test
   * @group document_folder
   */
  public function should_フォルダの新規作成()
  {
    $expects = [
      'name' => Str::random(50),
      'created_by' => $this->user->id,
    ];
    $response = $this->actingAs($this->user)->postJson(route('documentFolder.store'), $expects);
    $new = DocumentFolder::orderBy('id', 'desc')->first();
    $response->assertCreated()->assertJson([
      'id' => $new->id,
      'name' => $expects['name'],
    ]);
  }

  /**
   * @test
   * @group document_folder
   */
  public function should_フォルダ新規作成のバリデーションエラー()
  {
    $invalidData = [
      // 80文字を超えている
      'name' => Str::random(81),
      // ↓必須項目がない
      // 'created_by' => $this->user->id,
    ];
    $response = $this->actingAs($this->user)->postJson(route('documentFolder.store'), $invalidData);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'name', 'created_by'
    ]);
    $this->assertDatabaseMissing('document_folders', [
      'name' => $invalidData['name'],
    ]);
  }

  /**
   * @test
   * @group document_folder
   */
  public function should_作成者以外のユーザの更新禁止()
  {
    $folder = factory(DocumentFolder::class)->create([
      'created_by' => User::where('id', '!=', $this->user->id)->first()->id
    ]);
    $willDenied = [
      'name' => Str::random(80),
      'created_by' => $this->user->id,
    ];
    $response = $this->actingAs($this->user)->putJson(route('documentFolder.update', $folder), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('document_folders', [
      'name' => $willDenied['name'],
    ]);
  }

  /**
   * @test
   * @group document_folder
   */
  public function should_フォルダの更新()
  {
    $folder = factory(DocumentFolder::class)->create([
      'created_by' => $this->user->id,
    ]);
    $expects = [
      'name' => $folder->name . '_update',
      'created_by' => $this->user->id,
    ];
    $response = $this->actingAs($this->user)->putJson(route('documentFolder.update', $folder), $expects);
    $response->assertOk();
    $this->assertDatabaseHas('document_folders', [
      'id' => $folder->id,
      'name' => $expects['name'],
    ]);
  }

  /**
   * @test
   * @group document_folder
   */
  public function should_作成者以外のユーザによる削除禁止()
  {
    $folder = factory(DocumentFolder::class)->create([
      'created_by' => $this->user->id,
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();
    $response = $this->actingAs($badUser)->deleteJson(route('documentFolder.destroy', $folder));
    $response->assertForbidden();
    $this->assertDatabaseHas('document_folders', [
      'id' => $folder->id,
      'deleted_at' => null,
    ]);
  }

  /**
   * @test
   * @group document_folder
   */
  public function should_フォルダ削除()
  {
    $folder = factory(DocumentFolder::class)->create([
      'created_by' => $this->user->id,
    ]);
    $response = $this->actingAs($this->user)->deleteJson(route('documentFolder.destroy', $folder));
    $response->assertNoContent();
  }
}
