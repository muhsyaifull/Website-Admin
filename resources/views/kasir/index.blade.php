@extends('layouts.app')

@section('title', 'Dashboard Cashier')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store text-primary"></i> Dashboard Cashier
        </h1>
        <div class="text-right">
            <small class="text-muted">Cashier: {{ auth()->user()->name }}</small><br>
            <small class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</small>
        </div>
    </div>

    <!-- Quick Stats Cards Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 4px solid #7B3F2A!important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                                style="color: #7B3F2A!important;">Today's Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x" style="color: #7B3F2A;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Visitors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalParticipants }} orang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp
                                {{ number_format($totalRevenue, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Quick Action</div>
                            <a href="{{ route('kasir.booking.create') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-plus"></i> Create Booking
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plus-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour Sessions Overview -->
    <div class="row">
        @foreach($tours as $tour)
            @php $sessions = $tourSessions[$tour->id] ?? collect(); @endphp
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
                        style="background: #4e73df20;">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $tour->name }}</h6>
                        <small class="text-muted">
                            {{ $sessions->sum('booked') }}/{{ $sessions->sum('capacity') }} participants
                        </small>
                    </div>
                    <div class="card-body">
                        @forelse($sessions as $session)
                            <div class="mb-3 p-3 border rounded"
                                style="background: {{ $session->is_full ? '#F9F3F2' : '#FFFCFA' }}; border-color: {{ $session->is_full ? '#DDCCC8' : '#D0B8AD' }}!important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        @if($session->isCurrentlyActive())
                                            <span class="badge badge-success mr-2">IN PROGRESS</span>
                                        @endif
                                        <strong style="color: #4A2218;">{{ $session->label }}</strong>
                                    </div>
                                    <small class="text-muted">Guide:
                                        <strong>{{ $session->educator ? $session->educator->name : '-' }}</strong>
                                    </small>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $session->booking_percentage }}%; background-color: {{ $session->bar_color }};"
                                        aria-valuenow="{{ $session->booking_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $session->booked }}/{{ $session->capacity }} participants</small>
                                    <span class="badge"
                                        style="background: {{ $session->status_background }}; color: {{ $session->status_color }};">
                                        {{ $session->status }}
                                    </span>
                                </div>
                                @if($session->is_full)
                                    <small class="text-danger mt-1">
                                        <i class="fas fa-exclamation-triangle"></i> Full Slot — does not accept new participants
                                    </small>
                                @elseif($session->is_low)
                                    <small class="text-warning mt-1">
                                        <i class="fas fa-bolt"></i> Almost full — {{ $session->available }} seats remaining
                                    </small>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted text-center mb-0">No available sessions today</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Bookings -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
        </div>
        <div class="card-body">
            @if($todaysBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Booking Code</th>
                                <th>Representative</th>
                                <th>Package</th>
                                <th>Type</th>
                                <th>Participants</th>
                                <th>Sessions</th>
                                <th>Total</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todaysBookings->take(10) as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('kasir.booking.show', $booking) }}"
                                            class="text-decoration-none font-weight-bold">
                                            {{ $booking->booking_code }}
                                        </a>
                                    </td>
                                    <td>{{ $booking->representative_name }}</td>
                                    <td>{{ $booking->package->name }}</td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $booking->visitor_type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $booking->total_participants }} participants
                                        <small class="text-muted d-block">
                                            {{ $booking->adult_count }} adults,
                                            {{ $booking->child_count }} children
                                            @if($booking->infant_count > 0)
                                                , {{ $booking->infant_count }} infants
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @foreach($booking->bookingSessions as $session)
                                            <span class="badge badge-info">
                                                {{ $session->tour->name ?? 'Tour' }}: {{ $session->label }}
                                            </span>
                                        @endforeach
                                        @if($booking->bookingSessions->isEmpty())
                                            <span class="text-muted">No sessions available</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-success">{{ $booking->formatted_total_price }}</td>
                                    <td>{{ $booking->created_at->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No bookings available for today</p>
                    <a href="{{ route('kasir.booking.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create First Booking
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <small class="text-muted d-flex align-items-center flex-wrap">
                        <span class="mr-3"><span class="badge badge-success">●</span> Available</span>
                        <span class="mr-3"><span class="badge" style="background: #E67E22; color: white;">●</span> Nearly
                            Full (≤3)</span>
                        <span class="mr-3"><span class="badge badge-danger">●</span> Full</span>
                        <span class="mr-3"><span class="badge badge-primary">●</span> In Progress</span>
                    </small>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        setInterval(function () {
            location.reload();
        }, 30000);
    </script>
@endpush