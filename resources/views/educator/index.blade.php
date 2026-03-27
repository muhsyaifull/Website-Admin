@extends('layouts.app')

@section('title', 'Educator Dashboard')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-seedling text-success"></i> Educator Dashboard
        </h1>
        <div class="text-right">
            <small class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</small>
        </div>
    </div>

    <!-- Alert for Upcoming Sessions -->
    @if($upcomingSessions->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-clock"></i> <strong>Tour is about to start!</strong>
            @foreach($upcomingSessions as $session)
                <span class="badge badge-warning ml-1">{{ $session->label }} - {{ $session->educator->name }}</span>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Visitors Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVisitors }} visitors</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Today's Revenue</div>
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

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Active Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $allSessions = collect();
                                    foreach ($tourSessions as $sessions) {
                                        $allSessions = $allSessions->merge($sessions);
                                    }
                                @endphp
                                {{ $allSessions->where('is_active', true)->count() }}/{{ $allSessions->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Management Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="sessionTabs" role="tablist">
                @foreach($tours as $index => $tour)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tour-{{ $tour->id }}-tab" data-toggle="tab"
                            href="#tour-{{ $tour->id }}" role="tab">
                            {{ $tour->name }}
                            <style>
                                .nav-tabs .nav-link {
                                    color: #6c757d;
                                    /* normal */
                                }

                                .nav-tabs .nav-link.active {
                                    color: #af4324 !important;
                                    font-weight: 600;
                                    border-color: #dee2e6 #dee2e6 #fff;
                                }

                                .nav-tabs .nav-link:hover {
                                    color: #af4324;
                                }
                            </style>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="sessionTabsContent">
                @foreach($tours as $index => $tour)
                    @php
                        $sessions = ($tourSessions[$tour->id] ?? collect())
                            ->sortBy('start_time')
                            ->values();
                    @endphp
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tour-{{ $tour->id }}"
                        role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="font-weight-bold mb-0 text-primary">Today's Schedule —
                                {{ $tour->name }}
                            </h6>
                            <a href="{{ route('panel.sessions.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Add Session
                            </a>
                        </div>
                        @forelse($sessions as $session)
                            <div class="card mb-3 {{ $session->isCurrentlyActive() ? 'border-success' : ($session->is_full ? 'border-light' : 'border-light') }}"
                                style="background: {{ $session->isCurrentlyActive() ? '#4e73df10' : ($session->is_full ? '#F9F3F2' : '#FFFCFA') }};">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            @if($session->isCurrentlyActive())
                                                <span class="badge mr-2" style="background: #4f8a63; color: white;">IN
                                                    PROGRESS</span>
                                            @endif
                                            <h6 class="font-weight-bold mb-0 text-primary">
                                                {{ $session->label }}
                                            </h6>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-muted">Guide:
                                                <strong>{{ $session->educator ? $session->educator->name : '-' }}</strong></span>
                                            <div class="btn-group ml-2">
                                                <a href="{{ route('panel.sessions.edit', $session) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('panel.sessions.toggle', $session) }}" method="POST"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-outline-{{ $session->is_active ? 'warning' : 'success' }} btn-sm">
                                                        <i class="fas fa-{{ $session->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-2">
                                        <div class="progress flex-fill mr-3" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $session->booking_percentage }}%; background-color: {{ $session->bar_color }};">
                                            </div>
                                        </div>
                                        <span class="font-weight-bold text-right"
                                            style="color: {{ $session->is_full ? '#C0392B' : '#4e73df' }}; min-width: 100px;">
                                            {{ $session->booked }}/{{ $session->capacity }} participants
                                        </span>
                                    </div>

                                    @if($session->is_full)
                                        <small class="text-danger">Slot full — not accepting new participants</small>
                                    @elseif($session->is_low)
                                        <small class="text-warning">Almost full — {{ $session->available }} spots remaining</small>
                                    @endif

                                    @if(!$session->is_active)
                                        <small class="text-muted d-block mt-1"><i class="fas fa-pause"></i> Session deactivated</small>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">No sessions available for this tour today</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <small class="text-muted d-flex align-items-center flex-wrap">
                        <span class="mr-3"><span class="badge badge-success">●</span> Available</span>
                        <span class="mr-3"><span class="badge" style="background: #E67E22; color: white;">●</span> Almost
                            full (≤3)</span>
                        <span class="mr-3"><span class="badge badge-danger">●</span> Full</span>
                        <span class="mr-3"><span class="badge badge-primary">●</span> In progress</span>
                        <span class="mr-3"><span class="badge badge-secondary">●</span> Disabled</span>
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
        }, 60000);

        setInterval(function () {
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();

        }, 30000);
    </script>
@endpush