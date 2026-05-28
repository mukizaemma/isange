@extends('layouts.adminbase')

@section('title', $blog->exists ? 'Edit update' : 'New update')

@section('sidebar')
    @parent
@endsection

@section('content')
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('admin.includes.sidenav')
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Updates</a></li>
                        <li class="breadcrumb-item active">{{ $blog->exists ? 'Edit' : 'New' }}</li>
                    </ol>

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ $blog->exists ? 'Edit update' : 'Create update' }}</span>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary btn-sm">Back to list</a>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ $blog->exists ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" enctype="multipart/form-data">
                                @csrf
                                @if ($blog->exists)
                                    @method('PUT')
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label" for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $blog->title) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="slug">URL slug <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $blog->slug) }}" placeholder="auto-from-title">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="status">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="Published" @selected(old('status', $blog->status) === 'Published')>Published</option>
                                            <option value="Unpublished" @selected(old('status', $blog->status ?: 'Unpublished') === 'Unpublished')>Unpublished (draft)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="published_at">Publish date</label>
                                        <input type="datetime-local" class="form-control" id="published_at" name="published_at"
                                            value="{{ old('published_at', optional($blog->published_at)->format('Y-m-d\TH:i')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label" for="published_by">Author label</label>
                                        <input type="text" class="form-control" id="published_by" name="published_by" value="{{ old('published_by', $blog->published_by ?? 'Isange Paradise') }}">
                                    </div>
                                    @if ($blog->exists)
                                        <div class="col-md-4">
                                            <label class="form-label">Page views</label>
                                            <input type="text" class="form-control" value="{{ number_format($blog->views ?? 0) }}" readonly disabled>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <label class="form-label" for="blogBody">Content</label>
                                        <textarea id="blogBody" name="body" class="form-control" rows="12">{{ old('body', $blog->body) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="image">Featured image</label>
                                        @if ($blog->image)
                                            <div class="mb-2">
                                                <img src="{{ $blog->imageUrl() }}" alt="" class="img-thumbnail" style="max-height: 140px;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> {{ $blog->exists ? 'Save changes' : 'Create update' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if ($blog->exists)
                        <div class="card mb-4">
                            <div class="card-header">
                                <span class="fw-semibold">Comments ({{ $blog->comments->count() }})</span>
                            </div>
                            <div class="card-body">
                                @if ($blog->comments->isEmpty())
                                    <p class="text-muted mb-0">No comments yet.</p>
                                @else
                                    <div class="list-group list-group-flush">
                                        @foreach ($blog->comments as $comment)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
                                                        <strong>{{ $comment->author_name }}</strong>
                                                        @if ($comment->author_email)
                                                            <span class="text-muted small"> — {{ $comment->author_email }}</span>
                                                        @endif
                                                        <div class="small text-muted">{{ $comment->created_at->format('d M Y, H:i') }}</div>
                                                        <p class="mb-0 mt-2">{{ $comment->body }}</p>
                                                    </div>
                                                    <form action="{{ route('admin.blogs.comments.destroy', [$blog, $comment]) }}" method="POST" onsubmit="return confirm('Remove this comment from the website?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#blogBody').summernote({
            height: 280,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endsection
