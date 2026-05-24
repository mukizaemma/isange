@extends('layouts.adminbase')

@section('title', 'Menu categories')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">@include('admin.includes.sidenav')</div>
    <div id="layoutSidenav_content">
        <main class="ma-admin-page">
            <div class="container-fluid px-4 py-3">
                <div class="ma-page-head mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h1 class="ma-page-title">Menu categories</h1>
                        <p class="text-muted mb-0 small">Food &amp; beverage, bar, etc. Assign dishes to categories on the <strong>menu items</strong> page. Covers: AI or upload.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('diningMenu.manage') }}" class="btn btn-ma-primary btn-sm">
                            <i class="fas fa-book-open me-1"></i> Menu items
                        </a>
                        <a href="{{ route('diningMenu') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Dining page &amp; gallery
                        </a>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card ma-card mb-4">
                    <div class="card-header ma-card-head"><strong>Menu categories</strong> — Food &amp; beverage, bar, etc.</div>
                    <div class="card-body">
                        <p class="small text-muted mb-0">Assign each dish to a category from the menu items page. Set category covers with <strong>AI or upload</strong> using the cover button on each row (no image on your device required).</p>
                        <form action="{{ route('diningMenu.categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end mb-4 border-bottom pb-3">
                            @csrf
                            <div class="col-md-4"><label class="form-label">New category name</label><input type="text" name="name" class="form-control" required placeholder="Food &amp; beverage"></div>
                            <div class="col-md-4"><label class="form-label">Cover image (optional)</label><input type="file" name="cover_image" class="form-control" accept="image/*"></div>
                            <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Add</button></div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Cover</th><th>Name</th><th>Items</th><th>Cover</th><th></th></tr></thead>
                                <tbody>
                                    @foreach ($categories as $cat)
                                        <tr>
                                            <td style="width:88px;">
                                                @if ($cat->cover_image)
                                                    <img src="{{ asset('storage/images/menu-categories/'.$cat->cover_image) }}" alt="" class="rounded" style="width:72px;height:48px;object-fit:cover;">
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('diningMenu.categories.update', $cat) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap gap-2 align-items-center">
                                                    @csrf
                                                    <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm" style="max-width:220px;">
                                                    <input type="file" name="cover_image" class="form-control form-control-sm" style="max-width:200px;" accept="image/*">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                </form>
                                            </td>
                                            <td>{{ $cat->items_count }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#ma-ai-category-cover-modal"
                                                    data-ma-cat-id="{{ $cat->id }}"
                                                    data-ma-cat-name="{{ $cat->name }}"
                                                    data-ma-cover-url="{{ route('diningMenu.categories.coverUrl', $cat) }}"
                                                    data-ma-update-url="{{ route('diningMenu.categories.update', $cat) }}">
                                                    <i class="fas fa-image me-1"></i> AI &amp; upload…
                                                </button>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('diningMenu.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete category? Items become uncategorized.');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="ma-ai-category-cover-modal" tabindex="-1" aria-labelledby="ma-ai-category-cover-label" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ma-ai-category-cover-label">Category cover</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted small mb-2">Category: <strong id="ma-ai-modal-cat-name"></strong></p>
                                <p class="small mb-2">Generate options with AI, pick one, or upload a photo from your computer.</p>
                                <label class="form-label small mb-1" for="ma-ai-modal-context">Describe dishes or mood (helps AI)</label>
                                <textarea id="ma-ai-modal-context" class="form-control mb-2" rows="3" placeholder="e.g. grilled fish, fresh salads, evening cocktails…"></textarea>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="ma-ai-modal-generate">
                                    <i class="fas fa-magic me-1"></i> Generate images
                                </button>
                                <div id="ma-ai-modal-status" class="small text-muted mt-2"></div>
                                <div id="ma-ai-modal-gallery" class="row g-2 mt-3" role="list"></div>
                                <hr class="my-4">
                                <h6 class="mb-2">Upload from device</h6>
                                <form id="ma-ai-modal-upload-form" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="name" id="ma-ai-modal-upload-name" value="">
                                    <input type="file" name="cover_image" accept="image/*" class="form-control form-control-sm mb-2" required>
                                    <button type="submit" class="btn btn-success btn-sm">Save as cover</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var aiUrl = @json(route('diningMenu.aiImages'));
    var modalEl = document.getElementById('ma-ai-category-cover-modal');
    var galleryEl = document.getElementById('ma-ai-modal-gallery');
    var statusEl = document.getElementById('ma-ai-modal-status');
    var activeCoverUrl = '';

    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function (ev) {
            var btn = ev.relatedTarget;
            if (!btn || !btn.getAttribute) return;
            activeCoverUrl = btn.getAttribute('data-ma-cover-url') || '';
            var catName = btn.getAttribute('data-ma-cat-name') || '';
            document.getElementById('ma-ai-modal-cat-name').textContent = catName;
            document.getElementById('ma-ai-modal-upload-name').value = catName;
            document.getElementById('ma-ai-modal-upload-form').action = btn.getAttribute('data-ma-update-url') || '#';
            document.getElementById('ma-ai-modal-context').value = '';
            galleryEl.innerHTML = '';
            statusEl.textContent = '';
        });
    }

    document.getElementById('ma-ai-modal-generate').addEventListener('click', async function () {
        var catName = document.getElementById('ma-ai-modal-cat-name').textContent.trim();
        var summary = document.getElementById('ma-ai-modal-context').value.trim() || catName;
        if (!summary) {
            statusEl.innerHTML = '<span class="text-danger">Add a short description for AI, or rely on the category name.</span>';
            return;
        }
        statusEl.textContent = 'Generating…';
        galleryEl.innerHTML = '';
        try {
            var res = await fetch(aiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ menu_title: catName, items_summary: summary }),
            });
            var data = await res.json();
            if (!res.ok) {
                statusEl.innerHTML = '<span class="text-danger">' + (data.message || 'Failed') + '</span>';
                return;
            }
            statusEl.textContent = 'Click an image to set it as the category cover.';
            (data.urls || []).forEach(function (url) {
                var col = document.createElement('div');
                col.className = 'col-6 col-md-4';
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'btn btn-light border p-0 w-100 overflow-hidden rounded';
                b.setAttribute('aria-label', 'Use this image as cover');
                var img = document.createElement('img');
                img.src = url;
                img.alt = '';
                img.className = 'w-100 d-block';
                img.style.height = '140px';
                img.style.objectFit = 'cover';
                b.appendChild(img);
                b.addEventListener('click', async function () {
                    if (!activeCoverUrl) return;
                    statusEl.textContent = 'Saving…';
                    var r2 = await fetch(activeCoverUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ url: url }),
                    });
                    var d2 = await r2.json();
                    if (!r2.ok) {
                        statusEl.innerHTML = '<span class="text-danger">' + (d2.message || 'Could not save') + '</span>';
                        return;
                    }
                    window.location.reload();
                });
                col.appendChild(b);
                galleryEl.appendChild(col);
            });
            if (!data.urls || !data.urls.length) {
                statusEl.innerHTML = '<span class="text-warning">No images returned.</span>';
            }
        } catch (e) {
            statusEl.innerHTML = '<span class="text-danger">Network error</span>';
        }
    });
})();
</script>
@endsection
