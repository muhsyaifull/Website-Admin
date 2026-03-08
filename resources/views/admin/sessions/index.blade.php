@extends('layouts.app')

@section('title', 'Tour Sessions Management')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clock text-primary"></i> Tour Sessions Management
        </h1>
        <div>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-info">
                <i class="fas fa-layer-group"></i> Manage Template
            </a>
        </div>
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

    <!-- Sessions Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todaySessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Full Capacity</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $fullSessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today's Visitors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVisitors }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-walking fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Available Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookableSessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Options -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Sessions</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sessions.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_filter" class="form-label">Date Range</label>
                        <select name="date_filter" id="date_filter" class="form-control">
                            <option value="today" {{ request('date_filter', 'today') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="tomorrow" {{ request('date_filter') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                            <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="next_week" {{ request('date_filter') == 'next_week' ? 'selected' : '' }}>Next Week</option>
                            <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="type_filter" class="form-label">Tour Type</label>
                        <select name="type_filter" id="type_filter" class="form-control">
                            <option value="">All Types</option>
                            <option value="taman" {{ request('type_filter') == 'taman' ? 'selected' : '' }}>Taman Atsiri</option>
                            <option value="museum" {{ request('type_filter') == 'museum' ? 'selected' : '' }}>Museum Atsiri</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="educator_filter" class="form-label">Educator</label>
                        <select name="educator_filter" id="educator_filter" class="form-control">
                            <option value="">All Educators</option>
                            @foreach($educators as $educator)
                                <option value="{{ $educator->id }}" {{ request('educator_filter') == $educator->id ? 'selected' : '' }}>
                                    {{ $educator->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="capacity_filter" class="form-label">Capacity</label>
                        <select name="capacity_filter" id="capacity_filter" class="form-control">
                            <option value="">All Capacities</option>
                            <option value="available" {{ request('capacity_filter') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="full" {{ request('capacity_filter') == 'full' ? 'selected' : '' }}>Full</option>
                            <option value="near_full" {{ request('capacity_filter') == 'near_full' ? 'selected' : '' }}>Near Full (80%+)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary ml-2">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions List: Taman Atsiri -->
    @php $tamanSessions = $sessions->where('type', 'taman'); @endphp
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-seedling"></i> Taman Atsiri Tour Sessions
                <span class="badge badge-success ml-2">{{ $tamanSessions->count() }} sessions</span>
            </h6>
        </div>
        <div class="card-body">
            @if($tamanSessions->isEmpty())
                <p class="text-muted text-center my-3">No Taman Atsiri sessions found for this filter.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered sessionsTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Educator</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tamanSessions as $session)
                                @include('admin.sessions._session_row', ['session' => $session])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Sessions List: Museum Atsiri -->
    @php $museumSessions = $sessions->where('type', 'museum'); @endphp
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold" style="color: #7B3F2A;">
                <i class="fas fa-building"></i> Museum Atsiri Tour Sessions
                <span class="badge ml-2" style="background: #7B3F2A; color: white;">{{ $museumSessions->count() }} sessions</span>
            </h6>
        </div>
        <div class="card-body">
            @if($museumSessions->isEmpty())
                <p class="text-muted text-center my-3">No Museum Atsiri sessions found for this filter.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered sessionsTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Educator</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($museumSessions as $session)
                                @include('admin.sessions._session_row', ['session' => $session])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.sessionsTable').DataTable({
                "order": [[0, "asc"], [1, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [5] },
                    { "searchable": false, "targets": [3, 5] }
                ],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "emptyTable": "No sessions found",
                    "info": "Showing _START_ - _END_ of _TOTAL_ sessions",
                    "infoEmpty": "Showing 0 of 0 sessions",
                    "lengthMenu": "Show _MENU_ sessions per page",
                    "search": "Search sessions:",
                    "zeroRecords": "No sessions found"
                }
            });

            $('#date_filter, #type_filter, #educator_filter, #capacity_filter').change(function () {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush