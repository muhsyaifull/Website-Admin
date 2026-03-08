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
                                {{ $tamanSessions->where('is_active', true)->count() + $museumSessions->where('is_active', true)->count() }}/{{ $tamanSessions->count() + $museumSessions->count() }}
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
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="taman-tab" data-toggle="tab" href="#taman" role="tab">
                        <i class="fas fa-seedling text-success"></i> Tour Taman
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="museum-tab" data-toggle="tab" href="#museum" role="tab">
                        <i class="fas fa-building" style="color: #7B3F2A;"></i> Tour Museum
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="sessionTabsContent">
                <!-- Taman Sessions Tab -->
                <div class="tab-pane fade show active" id="taman" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold mb-0" style="color: #2C4A2C;">Today's Schedule — Tour Taman Atsiri</h6>
                        <a href="{{ route('educator.sessions.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add Session
                        </a>
                    </div>
                    @foreach($tamanSessions as $session)
                        <div class="card mb-3 {{ $session->isCurrentlyActive() ? 'border-success' : ($session->is_full ? 'border-light' : 'border-light') }}"
                            style="background: {{ $session->isCurrentlyActive() ? '#EBF2EB' : ($session->is_full ? '#F9F3F2' : '#FFFCFA') }};">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($session->isCurrentlyActive())
                                            <span class="badge badge-success mr-2">IN PROGRESS</span>
                                        @endif
                                        <h6 class="font-weight-bold mb-0" style="color: #2C4A2C;">{{ $session->label }}</h6>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-muted">Guide: <strong>{{ $session->educator->name }}</strong></span>
                                        <div class="btn-group ml-2">
                                            <a href="{{ route('educator.sessions.edit', $session) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('educator.sessions.toggle', $session) }}" method="POST"
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
                                        style="color: {{ $session->is_full ? '#C0392B' : '#2C4A2C' }}; min-width: 100px;">
                                        {{ $session->booked }}/{{ $session->capacity }} participants
                                    </span>
                                </div>

                                @if($session->is_full)
                                    <small class="text-danger">⚠️ Slot full — not accepting new participants</small>
                                @elseif($session->is_low)
                                    <small class="text-warning">⚡ Almost full — {{ $session->available }} spots remaining</small>
                                @endif

                                @if(!$session->is_active)
                                    <small class="text-muted d-block mt-1"><i class="fas fa-pause"></i> Session deactivated</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Museum Sessions Tab -->
                <div class="tab-pane fade" id="museum" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold mb-0" style="color: #7B3F2A;">Today's Schedule — Tour Museum Atsiri</h6>
                        <a href="{{ route('educator.sessions.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add Session
                        </a>
                    </div>
                    @foreach($museumSessions as $session)
                        <div class="card mb-3 {{ $session->isCurrentlyActive() ? 'border-primary' : ($session->is_full ? 'border-light' : 'border-light') }}"
                            style="background: {{ $session->isCurrentlyActive() ? '#F5EDE8' : ($session->is_full ? '#F9F3F2' : '#FFFCFA') }};">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($session->isCurrentlyActive())
                                            <span class="badge mr-2" style="background: #7B3F2A; color: white;">IN
                                                PROGRESS</span>
                                        @endif
                                        <h6 class="font-weight-bold mb-0" style="color: #7B3F2A;">{{ $session->label }}</h6>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-muted">Guide: <strong>{{ $session->educator->name }}</strong></span>
                                        <div class="btn-group ml-2">
                                            <a href="{{ route('educator.sessions.edit', $session) }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('educator.sessions.toggle', $session) }}" method="POST"
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
                                        style="color: {{ $session->is_full ? '#C0392B' : '#7B3F2A' }}; min-width: 100px;">
                                        {{ $session->booked }}/{{ $session->capacity }} participants
                                    </span>
                                </div>

                                @if($session->is_full)
                                    <small class="text-danger">⚠️ Fully booked — not accepting new participants</small>
                                @elseif($session->is_low)
                                    <small class="text-warning">⚡ Almost full — {{ $session->available }} spots remaining</small>
                                @endif

                                @if(!$session->is_active)
                                    <small class="text-muted d-block mt-1"><i class="fas fa-pause"></i> Session disabled</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
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
        // Auto refresh every 60 seconds for real-time updates
        setInterval(function () {
            location.reload();
        }, 60000);

        // Check for alerts every 30 seconds
        setInterval(function () {
            // Alert for sessions starting in 15 minutes
            const now = new Date();
            const currentTime = now.getHours() * 60 + now.getMinutes();

            // This could be enhanced with AJAX call to check upcoming sessions
        }, 30000);
    </script>
@endpush