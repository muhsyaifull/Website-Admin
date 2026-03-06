@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User Details</h1>
        <div>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit User
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Info Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Full Name:</label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Email Address:</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Role:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge badge-{{ $user->role == 'admin' ? 'success' : 'primary' }} badge-lg">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status:</label>
                                <p class="form-control-plaintext">
                                    <span
                                        class="badge badge-{{ $user->status == 'active' ? 'success' : 'danger' }} badge-lg">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Created At:</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('F d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Last Updated:</label>
                                <p class="form-control-plaintext">{{ $user->updated_at->format('F d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Avatar & Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Picture</h6>
                </div>
                <div class="card-body text-center">
                    <img class="img-profile rounded-circle mb-3"
                        src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150&background=5a5c69&color=ffffff"
                        style="width: 150px; height: 150px;">
                    <h5 class="font-weight-bold">{{ $user->name }}</h5>
                    <p class="text-muted">{{ ucfirst($user->role) }}</p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
</style>