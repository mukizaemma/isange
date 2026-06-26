<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Setting;
use App\Support\SpamProtection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogTest extends TestCase
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

    public function test_published_post_appears_on_updates_page_and_increments_views(): void
    {
        $post = Blog::create([
            'title' => 'Gorilla season tips',
            'slug' => 'gorilla-season-tips',
            'body' => '<p>Plan your trek early.</p>',
            'status' => 'Published',
            'published_by' => 'Isange Paradise',
            'published_at' => now()->subDay(),
            'views' => 0,
        ]);

        $this->get(route('blogs'))
            ->assertOk()
            ->assertSee('Gorilla season tips');

        $this->assertSame(0, $post->fresh()->views);

        $this->get(route('blog', $post->slug))
            ->assertOk()
            ->assertSee('Plan your trek early')
            ->assertSee('Leave a comment');

        $this->assertSame(1, $post->fresh()->views);

        $this->get(route('blog', $post->slug))
            ->assertOk();

        $this->assertSame(2, $post->fresh()->views);
    }

    public function test_unpublished_post_is_hidden_from_public_routes(): void
    {
        Blog::create([
            'title' => 'Draft only',
            'slug' => 'draft-only',
            'body' => '<p>Hidden</p>',
            'status' => 'Unpublished',
            'published_at' => now(),
            'views' => 0,
        ]);

        $this->get(route('blogs'))->assertOk()->assertDontSee('Draft only');
        $this->get(route('blog', 'draft-only'))->assertNotFound();
    }

    public function test_visitor_can_post_comment_on_published_article(): void
    {
        $post = Blog::create([
            'title' => 'Community news',
            'slug' => 'community-news',
            'body' => '<p>Hello</p>',
            'status' => 'Published',
            'published_at' => now(),
            'views' => 0,
        ]);

        $this->post(route('blog.comments.store', $post), array_merge([
            'author_name' => 'Jane Guest',
            'author_email' => 'jane@example.com',
            'body' => 'Lovely place!',
        ], SpamProtection::testFields()))
            ->assertRedirect(route('blog', $post).'#comments');

        $this->assertDatabaseHas('blog_comments', [
            'blog_id' => $post->id,
            'author_name' => 'Jane Guest',
            'body' => 'Lovely place!',
        ]);

        $this->get(route('blog', $post))
            ->assertOk()
            ->assertSee('Jane Guest')
            ->assertSee('Lovely place!');
    }

    public function test_home_page_lists_latest_published_updates(): void
    {
        Blog::create([
            'title' => 'Home visible post',
            'slug' => 'home-visible-post',
            'body' => '<p>On homepage</p>',
            'status' => 'Published',
            'published_at' => now(),
            'views' => 3,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Home visible post')
            ->assertSee('Stories from Isange Paradise');
    }
}
