@extends('layouts.app')

@section('title', 'User Detail')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4 ">
        <h1 class="h3 mb-0 text-gray-800">
            {{ $user->name }}
        </h1>
        <div>
            <a href="{{ route('panel.users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('panel.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Username</th>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email</th>
                            <td>{{ $user->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Phone</th>
                            <td>{{ $user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Role</th>
                            <td>
                                <span
                                    class="badge badge-{{ $user->isAdmin() ? 'danger' : ($user->isEducator() ? 'warning' : ($user->isMonitor() ? 'primary' : 'info')) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td>
                                <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Created</th>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection