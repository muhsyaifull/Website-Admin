@extends('layouts.app')

@section('title', 'Tour Sessions Management')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Tour Sessions Management
        </h1>
        <div>
            <a href="{{ route('panel.sessions.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus"></i> Add Session
            </a>
            <a href="{{ route('panel.templates.index') }}" class="btn btn-outline-info">
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
            <form method="GET" action="{{ route('panel.sessions.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <label for="type_filter" class="form-label">Tour Type</label>
                        <select name="type_filter" id="type_filter" class="form-control">
                            <option value="">All Types</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}" {{ request('type_filter') == $tour->id ? 'selected' : '' }}>
                                    {{ $tour->name }}
                                </option>
                            @endforeach
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
                        <a href="{{ route('panel.sessions.index') }}" class="btn btn-outline-secondary ml-2">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions List by Tour -->
    @foreach($tours as $tour)
        @php $tourSessions = $sessions->where('tour_id', $tour->id); @endphp
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ $tour->name }} Tour Sessions
                    <span class="badge ml-2" style="background: #af4324; color: white;">{{ $tourSessions->count() }} sessions</span>
                </h6>
            </div>
            <div class="card-body">
                @if($tourSessions->isEmpty())
                    <p class="text-muted text-center my-3">No {{ $tour->name }} sessions found for this filter.</p>
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
                                @foreach($tourSessions as $session)
                                    @include('admin.sessions._session_row', ['session' => $session])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

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