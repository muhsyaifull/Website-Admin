@extends('layouts.app')

@section('title', 'Manage Educators')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chalkboard-teacher text-primary"></i> Manage Educators
        </h1>
        <a href="{{ route('panel.educators.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Educator
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

    <!-- Educator Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Educators</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $educators->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactive Educators</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $educators->where('is_active', false)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Today's Sessions</div>
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
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Educators</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $educators->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Educator List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Educator List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="educatorsTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Educator</th>
                            <th>Specialization</th>
                            <th>Phone</th>
                            <th>Sessions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($educators as $educator)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3"
                                            style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ strtoupper(substr($educator->name, 0, 2)) }}
                                        </div>
                                        <div class="font-weight-bold">{{ $educator->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($educator->tours->count() > 0)
                                        @foreach($educator->tours as $tour)
                                            <span class="badge badge-primary">{{ $tour->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge badge-secondary">No specialization</span>
                                    @endif
                                </td>
                                <td>{{ $educator->phone ?? '-' }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $educator->sessions_count }} total</div>
                                    @if($educator->today_sessions > 0)
                                        <small class="text-primary">{{ $educator->today_sessions }} today</small>
                                    @endif
                                </td>
                                <td>
                                    @if($educator->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('panel.educators.show', $educator->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('panel.educators.edit', $educator->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($educator->sessions_count == 0)
                                            <form action="{{ route('panel.educators.destroy', $educator->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this educator?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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

    <!-- Today's Assignments -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-calendar-day"></i> Today's Assignments
            </h6>
        </div>
        <div class="card-body">
            @if($todayAssignments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Educator</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayAssignments as $assignment)
                                <tr>
                                    <td class="font-weight-bold">{{ $assignment->educator->name ?? '-' }}</td>
                                    <td><span class="badge badge-dark">{{ $assignment->label }}</span></td>
                                    <td>
                                        @if($assignment->tour)
                                            <span class="badge badge-primary">{{ $assignment->tour->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->booked }}/{{ $assignment->capacity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center my-3">No assignments today.</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#educatorsTable').DataTable({
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [5] },
                    { "searchable": false, "targets": [5] }
                ],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "emptyTable": "No educators found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ educators",
                    "infoEmpty": "Showing 0 of 0 educators",
                    "lengthMenu": "Show _MENU_ educators per page",
                    "search": "Search educator:",
                    "zeroRecords": "No matching educators found"
                }
            });
        });
    </script>
@endpush