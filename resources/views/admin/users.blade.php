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
                    <li class="breadcrumb-item active">Users</li>
                </ol>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-users me-1"></i> User accounts
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4">
                            Verify accounts, assign admin or staff roles, and reset passwords. Only the super admin can access this page.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Verified</th>
                                        <th>Role</th>
                                        <th style="min-width: 280px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                {{ $user->email }}
                                                @if ($user->isSuperAdmin())
                                                    <span class="badge bg-dark ms-1">Super admin</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->email_verified_at)
                                                    <span class="badge bg-success">Yes</span>
                                                    <div class="small text-muted">{{ $user->email_verified_at->format('M j, Y') }}</div>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-flex gap-2 align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="role" class="form-select form-select-sm" style="max-width: 120px;">
                                                        <option value="{{ \App\Models\User::ROLE_STAFF }}" @selected((string) $user->role === \App\Models\User::ROLE_STAFF)>Staff</option>
                                                        <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected((string) $user->role === \App\Models\User::ROLE_ADMIN)>Admin</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @if ($user->email_verified_at)
                                                        <form method="POST" action="{{ route('admin.users.unverify', $user) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Unverify</button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                                        </form>
                                                    @endif

                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-warning"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#reset-{{ $user->id }}"
                                                    >Reset password</button>
                                                </div>

                                                <div class="collapse mt-2" id="reset-{{ $user->id }}">
                                                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="border rounded p-3 bg-light">
                                                        @csrf
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-0">New password</label>
                                                            <input type="password" name="password" class="form-control form-control-sm" required autocomplete="new-password">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label small mb-0">Confirm password</label>
                                                            <input type="password" name="password_confirmation" class="form-control form-control-sm" required autocomplete="new-password">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-primary">Update password</button>
                                                    </form>
                                                </div>
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
    </div>
</div>
@endsection
