<?php

namespace Tests\Feature;

use App\Enums\ProcessFlag;
use App\Models\Blog;
use App\Models\BlogImage;
use App\Models\BlogTag;
use App\Models\Tag;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlogTest extends TestCase
{
  /**
   * @test
   * @group blog
   */
  public function should_ブログ一覧()
  {
    $response = $this->actingAs($this->user)->getJson(route('blog.index'));
    $response->assertOk(200)->assertJsonStructure([
      'data',
      'first_page_url',
      'from',
      'last_page',
      'last_page_url',
      'next_page_url',
      'path',
      'per_page',
      'prev_page_url',
      'to',
      'total',
    ]);
    $result = parent::$openApiValidator->validate('getBlog', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_ブログの投稿()
  {
    $tagCount = BlogTag::count();
    $imageCount = BlogImage::count();
    $faker = Factory::create('ja_JP');
    Storage::fake('s3');
    $expects = [
      'title' => Str::random(32),
      'body' => $faker->realText,
      'tags' => Arr::random(Tag::pluck('id')->toArray(), 2),
      'images' => [
        [
          'file' => UploadedFile::fake()->image('blog1.png', 100, 100)
        ],
        [
          'file' => UploadedFile::fake()->image('blog2.png', 100, 200)
        ],
        [
          'file' => UploadedFile::fake()->image('blog3.png', 200, 100)
        ],
        [
          'file' => UploadedFile::fake()->image('blog3.png', 300, 100)
        ],
      ]
    ];
    $response = $this->actingAs($this->user)->postJson(route('blog.store'), $expects);
    $response->assertCreated()->assertJson([
      'title' => $expects['title'],
    ]);
    $new = Blog::orderBy('id', 'desc')->first();
    $this->assertDatabaseHas('blogs', [
      'id' => json_decode($response->getContent(), true)['id'],
      'title' => json_decode($response->getContent(), true)['title'],
    ]);
    $new->images->each(function ($i) {
      Storage::disk('s3')->assertExists($i->file_path);
    });
    $this->assertDatabaseCount('blog_images', $imageCount + 4);
    $this->assertDatabaseCount('blog_tags', $tagCount + 2);

    $result = parent::$openApiValidator->validate('postBlog', 201, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_ブログ投稿時のバリエーションエラー()
  {
    $blogCount = Blog::count();
    $tagCount = BlogTag::count();
    $imageCount = BlogImage::count();

    Storage::fake('s3');
    $invalid = [
      // 'title', 'body'がない
      'tags' => [10000], // 存在しないタグID
      'images' => [
        [
          // 5MBバイトを超えている
          'file' => UploadedFile::fake()->image('blog1.png')->size(5121),
        ],
      ]
    ];
    $response = $this->actingAs($this->user)->postJson(route('blog.store'), $invalid);
    $response->assertStatus(422)->assertJsonValidationErrors([
      'title', 'body', 'tags.0', 'images.0.file',
    ]);
    $this->assertDatabaseCount('blogs', $blogCount);
    $this->assertDatabaseCount('blog_images', $imageCount);
    $this->assertDatabaseCount('blog_tags', $tagCount);

    $result = parent::$openApiValidator->validate('postBlog', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_ブログの更新()
  {
    $faker = Factory::create('ja_JP');
    Storage::fake('s3');
    $postData = [
      'title' => Str::random(32),
      'body' => $faker->realText,
      'tags' => Arr::random(Tag::pluck('id')->toArray(), 2),
      'images' => [
        [
          'file' => UploadedFile::fake()->image('blog1.png', 100, 100)
        ],
        [
          'file' => UploadedFile::fake()->image('blog2.png', 100, 200)
        ],
        [
          'file' => UploadedFile::fake()->image('blog3.png', 200, 100)
        ],
        [
          'file' => UploadedFile::fake()->image('blog3.png', 300, 100)
        ],
      ]
    ];
    $postResponse = $this->actingAs($this->user)->postJson(route('blog.store'), $postData);
    $blog = Blog::orderBy('id', 'desc')->first();
    $tagIds = $blog->tags->pluck('id')->toArray();
    $addedTag = Tag::whereNotIn('id', $tagIds)->first();
    $tagCount = BlogTag::count();
    $imageCount = BlogImage::count();

    $expects = [
      'title' => $blog->title . '_update',
      'body' => $blog->body . '_update',
      'tags' => array_merge([$addedTag->id], $tagIds),
      'images' => $this->imagePutData($blog->images),
    ];
    $response = $this->actingAs($this->user)->putJson(route('blog.update', $blog), $expects);
    $response->assertOk()->assertJson([
      'id' => $blog->id,
      'title' => $expects['title'],
    ]);
    $this->assertDatabaseCount('blog_tags', $tagCount + 1);
    // 1件ソフトデリートして1件追加したので+1
    $this->assertDatabaseCount('blog_images', $imageCount + 1)
      ->assertSoftDeleted('blog_images', [
        'id' => $blog->images->first()->id
      ]);
    $updated = Blog::orderBy('id', 'desc')->first();
    $updated->images->each(function ($i) {
      Storage::disk('s3')->assertExists($i->file_path);
    });

    $result = parent::$openApiValidator->validate('putBlogId', 200, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_投稿者以外の更新禁止()
  {
    $blog = factory(Blog::class)->create([
      'created_by' => $this->user->id,
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();
    $willDenied = [
      'title' => $blog->title . '_update',
      'body' => $blog->body . '_update',
    ];
    $response = $this->actingAs($badUser)->putJson(route('blog.update', $blog), $willDenied);
    $response->assertForbidden();
    $this->assertDatabaseMissing('blogs', [
      'id' => $blog->id,
      'title' => $willDenied['title'],
    ]);

    $result = parent::$openApiValidator->validate('putBlogId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_更新バリデーションエラー()
  {
    Storage::fake('s3');
    $blog = factory(Blog::class)->create([
      'created_by' => $this->user->id,
    ]);
    $invalid = [
      'created_by' => 12345,  // レコードに存在しないユーザーID
      'title' => Str::random(141),  // 制限文字数を1文字超えている
      'tags' => [12345], // レコードに存在しないタグID
      'images' => [
        [
          'id' => 12345,  // レコードに存在しない画像ID
          'file' => UploadedFile::fake()->image('invalid.html'),  // 拡張子が許可されていない
          'flag' => 12345,  // Enumに存在しないフラグ
        ]
      ],
    ];
    $response = $this->actingAs($this->user)->putJson(route('blog.update', $blog), $invalid);
    $response->assertJsonValidationErrors([
      'created_by', 'title', 'body', 'tags.0', 'images.0.id', 'images.0.file', 'images.0.flag',
    ]);
    $this->assertDatabaseMissing('blogs', [
      'id' => $blog->id,
      'title' => $invalid['title'],
    ]);

    $result = parent::$openApiValidator->validate('putBlogId', 422, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_ブログの削除()
  {
    $blog = factory(Blog::class)->create([
      'created_by' => $this->user->id,
    ]);
    $commentUser = User::where('id', '!=', $this->user->id)->first();
    $blog->comments()->create([
      'created_by' => $commentUser->id,
      'body' => 'Hi, I\'m' . $commentUser->given_name,
    ]);
    $response = $this->actingAs($this->user)->deleteJson(route('blog.destroy', $blog));
    $response->assertNoContent();
    $this->assertSoftDeleted('blogs', [
      'id' => $blog->id,
    ])->assertSoftDeleted('blog_comments', [
      'blog_id' => $blog->id,
    ]);

    $result = parent::$openApiValidator->validate('deleteBlogId', 204, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @test
   * @group blog
   */
  public function should_投稿者以外によるブログの削除禁止()
  {
    $blog = factory(Blog::class)->create([
      'created_by' => $this->user->id,
    ]);
    $badUser = User::where('id', '!=', $this->user->id)->first();
    $response = $this->actingAs($badUser)->deleteJson(route('blog.destroy', $blog));
    $response->assertForbidden();
    $this->assertDatabaseHas('blogs', [
      'id' => $blog->id,
      'deleted_at' => null,
    ]);

    $result = parent::$openApiValidator->validate('deleteBlogId', 403, json_decode($response->getContent(), true));
    $this->assertFalse($result->hasErrors(), $result);
  }

  /**
   * @param $images
   */
  private function imagePutData($images)
  {
    $current = $images->map(function ($image, $index) {
      return [
        'id' => $image->id,
        'file_path' => $image->file_path,
        'flag' => $index === 0 ? ProcessFlag::value('delete') : ProcessFlag::value('update'),
        'file' => $index === 1 ? UploadedFile::fake()->image('updateimage.png') : null,
      ];
    })->all();

    $new = [
      [
        'file' => UploadedFile::fake()->image('newimage.png'),
      ]
    ];
    return array_merge($current, $new);
  }
}
