@extends('layouts.app')

@section('title', 'Dashboard Admin IT')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs text-primary"></i> Dashboard Admin IT
        </h1>
        <div class="text-right">
            <small class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</small>
            <div class="text-muted">Welcome, {{ auth()->user()->name }}</div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Bookings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings }}</div>
                            <div class="text-xs text-muted mt-1">Today: {{ $todaysBookings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                {{ number_format($totalRevenue, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-muted mt-1">Today: Rp
                                {{ number_format($todaysRevenue, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('panel.users.create') }}" class="btn btn-primary btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                <span class="text">Add New User</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('panel.packages.create') }}" class="btn btn-success btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-box"></i>
                                </span>
                                <span class="text">Create Package</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('panel.sessions.index') }}" class="btn btn-info btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <span class="text">Add Session</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('panel.bookings.index') }}" class="btn btn-warning btn-icon-split btn-block">
                                <span class="icon text-white-50">
                                    <i class="fas fa-list"></i>
                                </span>
                                <span class="text">View All Bookings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Row -->
    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                    <a href="{{ route('panel.bookings.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        @foreach($recentBookings as $booking)
                            <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-calendar-check text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-gray-500">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                                    <div class="font-weight-bold">{{ $booking->booking_code }}</div>
                                    <div class="text-sm">{{ $booking->package->name }} - {{ $booking->total_participants }}
                                        participants
                                    </div>
                                    <div class="text-success font-weight-bold">{{ $booking->formatted_total_price }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-{{ $booking->status_color }}">{{ $booking->status_label }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <div>No bookings today</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    <a href="{{ route('panel.users.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        @foreach($recentUsers as $user)
                            <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="mr-3">
                                    <div class="rounded-circle bg-{{ $user->isAdmin() ? 'danger' : ($user->isEducator() ? 'warning' : 'info') }} d-flex align-items-center justify-content-center"
                                        style="width: 35px; height: 35px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                    <div class="small text-gray-500">{{ $user->created_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="badge badge-{{ $user->isAdmin() ? 'danger' : ($user->isEducator() ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                    <div class="mt-1">
                                        <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <div>No new users yet</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h5 class="font-weight-bold">
                                    {{ \App\Models\User::whereIn('role', ['cashier', 'educator', 'admin'])->count() }}
                                </h5>
                                <p class="text-muted mb-0">System Users</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-box fa-2x text-success mb-2"></i>
                                <h5 class="font-weight-bold">{{ \App\Models\Package::count() }}</h5>
                                <p class="text-muted mb-0">Tour Packages</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h5 class="font-weight-bold">
                                    {{ \App\Models\TourSession::whereDate('date', Carbon\Carbon::today())->fromActiveTemplate()->count() }}
                                </h5>
                                <p class="text-muted mb-0">Today's Sessions</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <i class="fas fa-chalkboard-teacher fa-2x text-info mb-2"></i>
                                <h5 class="font-weight-bold">{{ \App\Models\Educator::count() }}</h5>
                                <p class="text-muted mb-0">Educators</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        setInterval(function () {
            location.reload();
        }, 300000);

        $(document).ready(function () {
        });
    </script>
@endpush