<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::query()
            ->withCount('comments')
            ->with(['comments' => fn ($q) => $q->latest()->limit(3)])
            ->latest('published_at')
            ->latest('id')
            ->get();

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.form', ['blog' => new Blog]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBlog($request);
        $blog = new Blog;
        $this->fillBlog($blog, $validated, $request);
        $blog->added_by = Auth::id();
        $blog->save();

        return redirect()->route('admin.blogs.edit', $blog)->with('success', 'Update created.');
    }

    public function edit(Blog $blog)
    {
        $blog->load(['comments' => fn ($q) => $q->latest()]);

        return view('admin.blogs.form', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $this->validateBlog($request, $blog);
        $this->fillBlog($blog, $validated, $request);
        $blog->save();

        return redirect()->route('admin.blogs.edit', $blog)->with('success', 'Update saved.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            $path = 'public/images/blogs/'.ltrim($blog->image, '/');
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Update deleted.');
    }

    public function destroyComment(Blog $blog, BlogComment $comment)
    {
        abort_unless($comment->blog_id === $blog->id, 404);

        $comment->delete();

        return redirect()->route('admin.blogs.edit', $blog)->with('success', 'Comment removed.');
    }

    public function destroyAllComments(Blog $blog)
    {
        $count = $blog->comments()->count();

        if ($count === 0) {
            return redirect()->route('admin.blogs.edit', $blog)->with('success', 'No comments to remove.');
        }

        $blog->comments()->delete();

        return redirect()->route('admin.blogs.edit', $blog)->with('success', $count === 1 ? 'Comment removed.' : "{$count} comments removed.");
    }

    private function validateBlog(Request $request, ?Blog $blog = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($blog?->id),
            ],
            'body' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Published', 'Unpublished'])],
            'published_by' => ['nullable', 'string', 'max:120'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
        ]);
    }

    private function fillBlog(Blog $blog, array $validated, Request $request): void
    {
        $blog->title = $validated['title'];
        $blog->slug = $this->uniqueSlug(
            $validated['slug'] ?? Str::slug($validated['title']),
            $blog->id
        );
        $blog->body = $validated['body'] ?? '';
        $blog->status = $validated['status'];
        $blog->published_by = $validated['published_by'] ?? ($blog->published_by ?: 'Isange Paradise');
        $blog->published_at = $validated['published_at'] ?? ($blog->published_at ?: now());

        if ($blog->status === 'Published' && ! $blog->published_at) {
            $blog->published_at = now();
        }

        if ($request->hasFile('image')) {
            if ($blog->image) {
                $old = 'public/images/blogs/'.ltrim($blog->image, '/');
                if (Storage::exists($old)) {
                    Storage::delete($old);
                }
            }
            $path = $request->file('image')->store('public/images/blogs');
            $blog->image = basename($path);
        }
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($slug) ?: 'update';
        $base = $slug;
        $n = 1;

        while (Blog::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$n;
            $n++;
        }

        return $slug;
    }
}
