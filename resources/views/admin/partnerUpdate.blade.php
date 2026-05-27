@extends('layouts.adminbase')

@section('title', 'Edit partner')

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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('partnerCrud') }}">Partners</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                        <a href="{{ route('partnerCrud') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
                    </div>

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
                        <div class="card-header fw-semibold">Edit partner</div>
                        <div class="card-body">
                            <form action="{{ route('updatePartner', $partner->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label d-block">Current logo</label>
                                        <img src="{{ asset('storage/images/partners/' . $partner->image) }}"
                                            alt="{{ $partner->title ?? 'Partner' }}"
                                            class="img-fluid border rounded p-2 bg-white"
                                            style="max-height: 120px; object-fit: contain;">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="partner_logo" class="form-label">Replace logo <span class="text-muted">(optional)</span></label>
                                            <input type="file" name="image" id="partner_logo" class="form-control" accept="image/*">
                                        </div>
                                        <div class="mb-3">
                                            <label for="partner_title" class="form-label">Name <span class="text-muted">(optional)</span></label>
                                            <input type="text" name="title" id="partner_title" class="form-control" maxlength="120"
                                                value="{{ old('title', $partner->title) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="partner_website" class="form-label">Website URL <span class="text-muted">(optional)</span></label>
                                            <input type="url" name="website" id="partner_website" class="form-control" maxlength="500"
                                                value="{{ old('website', $partner->website) }}" placeholder="https://example.com">
                                        </div>
                                        <div class="mb-3">
                                            <label for="partner_description" class="form-label">Notes <span class="text-muted">(optional)</span></label>
                                            <textarea name="description" id="partner_description" class="form-control" rows="3" maxlength="5000">{{ old('description', $partner->description) }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.includes.footer')
        </div>
    </div>
@endsection
