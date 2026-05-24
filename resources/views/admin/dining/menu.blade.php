@extends('layouts.adminbase')

@section('title', 'Restaurant menu — items')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">@include('admin.includes.sidenav')</div>
    <div id="layoutSidenav_content">
        <main class="ma-admin-page">
            <div class="container-fluid px-4 py-3">
                <div class="ma-page-head mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h1 class="ma-page-title">Restaurant menu</h1>
                        <p class="text-muted mb-0 small">Dishes and prices for the public <strong>/dining</strong> page. Manage categories separately.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('diningMenu.categories.manage') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-layer-group me-1"></i> Menu categories
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
                    <div class="card-header ma-card-head"><strong>Menu items</strong></div>
                    <div class="card-body">
                        <form action="{{ route('diningMenu.items.store') }}" method="POST" enctype="multipart/form-data" class="row g-2 mb-4 border-bottom pb-4">
                            @csrf
                            <div class="col-md-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Price (USD)</label><input type="number" step="0.01" min="0" name="price_usd" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Price (RWF)</label><input type="number" step="1" min="0" name="price_rwf" class="form-control" placeholder="Optional"></div>
                            <div class="col-md-2"><label class="form-label">Prep (min)</label><input type="number" step="1" min="1" max="600" name="prep_minutes" class="form-control" placeholder="Optional"></div>
                            <div class="col-md-2"><label class="form-label">Category</label>
                                <select name="menu_category_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                            <div class="col-md-1 d-flex align-items-end"><button type="submit" class="btn btn-success w-100">Add</button></div>
                            <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2" placeholder="Ingredients, dietary notes…"></textarea></div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead><tr><th></th><th>Title / description</th><th></th></tr></thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr>
                                            <td style="width:72px;">
                                                @if ($item->image)
                                                    <img src="{{ asset('storage/images/dining/'.$item->image) }}" alt="" class="rounded" style="width:64px;height:48px;object-fit:cover;">
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('diningMenu.items.update', $item) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                                    @csrf
                                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                                        <input type="text" name="title" value="{{ $item->title }}" class="form-control form-control-sm" style="max-width:220px;">
                                                        <input type="number" step="0.01" name="price_usd" value="{{ $item->price_usd }}" class="form-control form-control-sm" style="width:100px;">
                                                        <input type="number" step="1" name="price_rwf" value="{{ $item->price_rwf }}" class="form-control form-control-sm" style="width:90px;" placeholder="RWF">
                                                        <input type="number" step="1" min="1" max="600" name="prep_minutes" value="{{ $item->prep_minutes }}" class="form-control form-control-sm" style="width:72px;" placeholder="min" title="Prep minutes">
                                                        <select name="menu_category_id" class="form-select form-select-sm" style="max-width:180px;">
                                                            <option value="">— None —</option>
                                                            @foreach ($categories as $c)
                                                                <option value="{{ $c->id }}" @selected($item->menu_category_id == $c->id)>{{ $c->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="file" name="image" class="form-control form-control-sm" style="max-width:200px;">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                                    </div>
                                                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Description">{{ old('description_'.$item->id, $item->description) }}</textarea>
                                                </form>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('diningMenu.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this item?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>
@endsection
