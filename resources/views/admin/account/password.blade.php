@extends('layouts.adminbase')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Change password</li>
                </ol>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card mb-4" style="max-width: 520px;">
                    <div class="card-header">
                        <i class="fas fa-key me-1"></i> Change your password
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current password</label>
                                <input type="password" id="current_password" name="current_password" class="form-control" required autocomplete="current-password">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password">
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm new password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
