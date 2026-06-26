<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlogCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_all_comments_on_a_post(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $blog = Blog::create([
            'title' => 'Community news',
            'slug' => 'community-news',
            'body' => '<p>Hello</p>',
            'status' => 'Published',
            'published_at' => now(),
            'views' => 0,
        ]);

        BlogComment::create([
            'blog_id' => $blog->id,
            'author_name' => 'A',
            'body' => 'Spam one',
        ]);

        BlogComment::create([
            'blog_id' => $blog->id,
            'author_name' => 'B',
            'body' => 'Spam two',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.blogs.comments.destroy-all', $blog))
            ->assertRedirect(route('admin.blogs.edit', $blog))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('blog_comments', 0);
    }

    public function test_admin_updates_table_shows_posts_with_comments(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $blog = Blog::create([
            'title' => 'Apartment Stay In Musanze',
            'slug' => 'apartment-stay-musanze',
            'body' => '<p>Hello</p>',
            'status' => 'Published',
            'published_at' => now(),
            'views' => 0,
        ]);

        BlogComment::create([
            'blog_id' => $blog->id,
            'author_name' => 'A',
            'body' => 'Spam one',
        ]);

        BlogComment::create([
            'blog_id' => $blog->id,
            'author_name' => 'B',
            'body' => 'Spam two',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.blogs.index'))
            ->assertOk()
            ->assertSee('Apartment Stay In Musanze')
            ->assertSee('2 comments')
            ->assertSee('Latest:')
            ->assertSee('Manage comments');
    }
}
