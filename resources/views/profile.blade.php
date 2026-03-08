@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user text-primary"></i> Profile
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                </div>
                <div class="card-body text-center">
                    <img class="img-profile rounded-circle mb-3"
                        src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=5a5c69&color=ffffff&size=120"
                        width="120" height="120">

                    <table class="table table-borderless text-left mt-3">
                        <tr>
                            <th width="35%"><i class="fas fa-user fa-fw mr-2 text-gray-400"></i> Name</th>
                            <td>{{ auth()->user()->name }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-id-badge fa-fw mr-2 text-gray-400"></i> Username</th>
                            <td>{{ auth()->user()->username }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-envelope fa-fw mr-2 text-gray-400"></i> Email</th>
                            <td>{{ auth()->user()->email }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-shield-alt fa-fw mr-2 text-gray-400"></i> Role</th>
                            <td>
                                @php
                                    $role = auth()->user()->role;
                                    $badgeClass = match ($role) {
                                        'admin' => 'badge-danger',
                                        'educator' => 'badge-success',
                                        'cashier' => 'badge-info',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2">{{ ucfirst($role) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection