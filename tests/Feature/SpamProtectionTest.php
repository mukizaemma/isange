<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $setting = new Setting;
        $setting->forceFill([
            'title' => 'Isange Paradise',
            'company' => 'Isange Paradise',
            'email' => 'stay@example.com',
            'phone' => '+250000000000',
        ]);
        $setting->save();
    }

    public function test_blog_comment_rejects_honeypot_submissions(): void
    {
        $post = Blog::create([
            'title' => 'Community news',
            'slug' => 'community-news',
            'body' => '<p>Hello</p>',
            'status' => 'Published',
            'published_at' => now(),
            'views' => 0,
        ]);

        $this->from(route('blog', $post))
            ->post(route('blog.comments.store', $post), array_merge([
                'author_name' => 'Bot',
                'body' => 'Spam comment',
                '_hp_website' => 'https://spam.example',
            ], [
                '_form_ts' => time() - 5,
            ]))
            ->assertRedirect(route('blog', $post).'#comments')
            ->assertSessionHasErrors();

        $this->assertDatabaseMissing('blog_comments', [
            'blog_id' => $post->id,
            'author_name' => 'Bot',
        ]);
    }

    public function test_blog_comment_rejects_immediate_submissions(): void
    {
        $post = Blog::create([
            'title' => 'Community news',
            'slug' => 'community-news-2',
            'body' => '<p>Hello</p>',
            'status' => 'Published',
            'published_at' => now(),
            'views' => 0,
        ]);

        $this->from(route('blog', $post))
            ->post(route('blog.comments.store', $post), [
                'author_name' => 'Bot',
                'body' => 'Spam comment',
                '_hp_website' => '',
                '_form_ts' => time(),
            ])
            ->assertRedirect(route('blog', $post).'#comments')
            ->assertSessionHasErrors('submission');

        $this->assertDatabaseMissing('blog_comments', [
            'blog_id' => $post->id,
            'author_name' => 'Bot',
        ]);
    }
}
