@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users text-primary"></i> User Management
        </h1>
        <a href="{{ route('panel.users.create') }}" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Add New User</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Users Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All System Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="usersTable" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-{{ $user->isAdmin() ? 'danger' : ($user->isEducator() ? 'warning' : 'info') }} d-flex align-items-center justify-content-center mr-2"
                                            style="width: 30px; height: 30px;">
                                            <i class="fas fa-user text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->username ?? '-' }}</td>
                                <td>{{ $user->email ?? '-' }}</td>
                                <td>
                                    <span
                                        class="badge badge-{{ $user->isAdmin() ? 'danger' : ($user->isEducator() ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    {{ $user->last_login ? $user->last_login->format('d M Y, H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('panel.users.show', $user) }}" class="btn btn-sm btn-outline-info"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('panel.users.edit', $user) }}" class="btn btn-sm btn-outline-warning"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('panel.users.destroy', $user) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    @if($user->bookings()->count() > 0) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                "order": [[5, "desc"]], // Sort by created date
                "columnDefs": [
                    { "orderable": false, "targets": [7] }, // Disable sorting on Actions column
                    { "searchable": false, "targets": [7] }  // Disable search on Actions column
                ],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "search": "Search Users:",
                    "lengthMenu": "Show _MENU_ users per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ users",
                    "paginate": {
                        "previous": "Previous",
                        "next": "Next"
                    }
                }
            });
        });
    </script>
@endpush