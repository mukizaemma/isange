@extends('layouts.adminbase')

@section('title', 'Updates')

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
                        <li class="breadcrumb-item active">Updates / blog posts</li>
                    </ol>

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($blogs->isNotEmpty())
                        @php
                            $totalViews = $blogs->sum('views');
                            $totalComments = $blogs->sum('comments_count');
                        @endphp
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <div class="text-muted small">Published posts</div>
                                        <div class="fs-4 fw-semibold">{{ $blogs->where('status', 'Published')->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <div class="text-muted small">Total page views</div>
                                        <div class="fs-4 fw-semibold">{{ number_format($totalViews) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <div class="text-muted small">Total comments</div>
                                        <div class="fs-4 fw-semibold">{{ number_format($totalComments) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="fw-semibold">All updates</span>
                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New update
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($blogs->isEmpty())
                                <p class="text-muted mb-0">No updates yet. Create one to show it on the public Updates page.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th class="text-end">Views</th>
                                                <th class="text-end">Comments</th>
                                                <th>Published</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($blogs as $blog)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="fw-semibold text-decoration-none">{{ $blog->title }}</a>
                                                        @if ($blog->isPublished())
                                                            <div class="small text-muted">
                                                                <a href="{{ route('blog', $blog) }}" target="_blank" rel="noopener">View on site</a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($blog->status === 'Published')
                                                            <span class="badge bg-success">Published</span>
                                                        @else
                                                            <span class="badge bg-secondary">Draft</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">{{ number_format($blog->views ?? 0) }}</td>
                                                    <td class="text-end">{{ $blog->comments_count }}</td>
                                                    <td>{{ optional($blog->published_at)->format('d M Y') ?? '—' }}</td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this update and all its comments?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>
@endsection
