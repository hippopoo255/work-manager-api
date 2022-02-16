<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlogCommentTest extends TestCase
{
  private $blog;

  protected function setUp(): void
  {
    parent::setUp();
    $this->blog = factory(Blog::class)->create([
      'created_by' => $this->user->id,
    ]);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_コメント投稿()
  {
    $count = BlogComment::count();
    $expects = [
      'created_by' => $this->user->id,
      'body' => 'This is my first blog.',
    ];
    $response = $this->actingAs($this->user)->postJson(route('blogComment.store', $this->blog), $expects);

    $comment = BlogComment::orderBy('id', 'desc')->first();
    $response->assertCreated()->assertJson([
      'id' => $comment->id,
      'body' => $expects['body'],
    ]);
    $this->assertDatabaseCount('blog_comments', $count + 1);

    $result = parent::$openApiValidator->validate('postBlogIdComment', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_投稿バリデーションエラー()
  {
    $invalid = [
      'created_by' => 12345, // レコードに存在しないユーザID
      'body' => Str::random(141), // 制限字数を1文字超えている
    ];
    $response = $this->actingAs($this->user)->postJson(route('blogComment.store', $this->blog), $invalid);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'created_by', 'body'
    ]);

    $result = parent::$openApiValidator->validate('postBlogIdComment', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_コメントの更新()
  {
    $comment = BlogComment::create([
      'created_by' => $this->user->id,
      'blog_id' => $this->blog->id,
      'body' => Str::random(64),
    ]);

    $count = BlogComment::count();
    $expects = [
      'created_by' => $this->user->id,
      'body' => $comment->body . '_update',
    ];
    $response = $this->actingAs($this->user)->putJson(route('blogComment.update', [
      'id' => $this->blog,
      'comment_id' => $comment,
    ]), $expects);

    $response->assertOk()->assertJson([
      'id' => $comment->id,
      'body' => $expects['body'],
    ]);
    $this->assertDatabaseCount('blog_comments', $count);

    $result = parent::$openApiValidator->validate('putBlogIdComment', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_コメント投稿者以外のユーザは編集禁止()
  {
    $comment = BlogComment::create([
      'created_by' => $this->user->id,
      'blog_id' => $this->blog->id,
      'body' => Str::random(64),
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();
    $willDenied = [
      'created_by' => $badUser->id,
      'body' => $comment->body . '_update',
    ];
    $response = $this->actingAs($badUser)->putJson(route('blogComment.update', [
      'id' => $this->blog,
      'comment_id' => $comment,
    ]), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('blog_comments', [
      'id' => $comment->id,
      'body' => $willDenied['body'],
    ]);

    $result = parent::$openApiValidator->validate('putBlogIdComment', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_コメント投稿者によるコメントの削除()
  {
    $comment = BlogComment::create([
      'created_by' => $this->user->id,
      'blog_id' => $this->blog->id,
      'body' => Str::random(64),
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('blogComment.destroy', [
      'id' => $this->blog,
      'comment_id' => $comment,
    ]));
    $response->assertNoContent();
    $this->assertSoftDeleted('blog_comments', [
      'id' => $comment->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteBlogIdComment', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_ブログ投稿者によるコメントの削除()
  {
    $commentWriter = User::where('id', '!=', $this->user->id)->first();
    $comment = BlogComment::create([
      'created_by' => $commentWriter->id,
      'blog_id' => $this->blog->id,
      'body' => Str::random(64),
    ]);

    $response = $this->actingAs($this->user)->deleteJson(route('blogComment.destroy', [
      'id' => $this->blog,
      'comment_id' => $comment,
    ]));
    $response->assertNoContent();
    $this->assertSoftDeleted('blog_comments', [
      'id' => $comment->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteBlogIdComment', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog_comment
   */
  public function should_ブログまたはコメント投稿者以外によるコメントの削除禁止()
  {
    $comment = BlogComment::create([
      'created_by' => $this->user->id,
      'blog_id' => $this->blog->id,
      'body' => Str::random(64),
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();

    $response = $this->actingAs($badUser)->deleteJson(route('blogComment.destroy', [
      'id' => $this->blog,
      'comment_id' => $comment,
    ]));
    $response->assertForbidden();
    $this->assertDatabaseHas('blog_comments', [
      'id' => $comment->id,
      'deleted_at' => null,
    ]);

    $result = parent::$openApiValidator->validate('deleteBlogIdComment', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }
}
