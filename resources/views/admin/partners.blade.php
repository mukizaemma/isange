@extends('layouts.adminbase')

@section('title', 'Partners')

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
                        <li class="breadcrumb-item active">Partners</li>
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
                            <span class="fw-semibold">Partner logos (footer)</span>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#partnerAddModal">
                                <i class="fa fa-plus"></i> Add partner
                            </button>
                        </div>
                        <div class="card-body">
                            @if ($partners->isEmpty())
                                <p class="text-muted mb-0">No partners yet. Add a logo to display it above the site footer.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Logo</th>
                                                <th>Name</th>
                                                <th>Website</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($partners as $partner)
                                                <tr>
                                                    <td>
                                                        <img src="{{ asset('storage/images/partners/' . $partner->image) }}"
                                                            alt="{{ $partner->title ?? 'Partner' }}"
                                                            style="height: 48px; width: auto; max-width: 140px; object-fit: contain;">
                                                    </td>
                                                    <td>{{ $partner->title ?: '—' }}</td>
                                                    <td>
                                                        @if ($partner->website)
                                                            <a href="{{ $partner->website }}" target="_blank" rel="noopener">{{ $partner->website }}</a>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('editPartner', $partner->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                        <a href="{{ route('destroyPartner', $partner->id) }}" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Delete this partner?')">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal fade" id="partnerAddModal" tabindex="-1" aria-labelledby="partnerAddModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="partnerAddModalLabel">Add partner</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('savePartner') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="partner_logo" class="form-label">Logo <span class="text-danger">*</span></label>
                                            <input type="file" name="image" id="partner_logo" class="form-control" accept="image/*" required>
                                            <div class="form-text">PNG or SVG with transparent background works best. Logos display at a fixed height on the site.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="partner_title" class="form-label">Name <span class="text-muted">(optional)</span></label>
                                            <input type="text" name="title" id="partner_title" class="form-control" maxlength="120" placeholder="Partner or organisation name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="partner_website" class="form-label">Website URL <span class="text-muted">(optional)</span></label>
                                            <input type="url" name="website" id="partner_website" class="form-control" maxlength="500" placeholder="https://example.com">
                                            <div class="form-text">Opens in a new tab when visitors click the logo.</div>
                                        </div>
                                        <div class="mb-0">
                                            <label for="partner_description" class="form-label">Notes <span class="text-muted">(optional, admin only)</span></label>
                                            <textarea name="description" id="partner_description" class="form-control" rows="3" maxlength="5000"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save partner</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>
@endsection
