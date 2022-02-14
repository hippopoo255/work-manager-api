<?php

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\User;
use App\Models\Tag;

class BlogSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('blogs')->truncate();
    DB::table('blog_tags')->truncate();

    $users = User::all();
    $blogCount = 80;
    $perUser = (int)ceil($blogCount / $users->count());
    $users->each(function ($user) use ($perUser) {
      $blogs = factory(Blog::class, $perUser)->create([
        'created_by' => $user->id,
        'title' => $user->family_name . 'です',
      ]);
      $blogs->each(function ($blog) {
        // タグ0~2個
        $tagIds = Tag::all()->pluck('id')->shuffle()->splice(0, random_int(0, 2))->toArray();
        $blog->tags()->sync($tagIds);
      });
    });
  }
}
