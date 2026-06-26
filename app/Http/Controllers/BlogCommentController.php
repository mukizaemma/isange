<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Support\SpamProtection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BlogCommentController extends Controller
{
    public function store(Request $request, Blog $blog)
    {
        abort_unless($blog->isPublished(), 404);

        SpamProtection::validateRequest(
            $request,
            route('blog', $blog).'#comments'
        );

        try {
            $validated = $request->validate([
                'author_name' => ['required', 'string', 'max:120'],
                'author_email' => ['nullable', 'email', 'max:255'],
                'body' => ['required', 'string', 'max:5000'],
            ]);
        } catch (ValidationException $e) {
            throw $e->redirectTo(
                route('blog', $blog).'#comments'
            );
        }

        BlogComment::create([
            'blog_id' => $blog->id,
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'] ?? null,
            'body' => $validated['body'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Thank you for your comment.']);
        }

        return redirect()
            ->route('blog', $blog)
            ->withFragment('comments')
            ->with('comment_success', 'Thank you — your comment has been posted.');
    }
}
