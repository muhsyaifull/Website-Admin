@extends('layouts.app')

@section('title', 'Manage Tours')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Manage Tours
        </h1>
        <a href="{{ route('panel.tours.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Tour
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

    <!-- Tour Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tours->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Tours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $tours->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Packages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tours->sum('packages_count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tours->sum('tour_sessions_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tour List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tour List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="toursTable" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Tour</th>
                            <th>Packages</th>
                            <th>Templates</th>
                            <th>Sessions</th>
                            <th>Educators</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tours as $tour)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $tour->name }}</div>
                                    @if($tour->description)
                                        <small class="text-muted">{{ Str::limit($tour->description, 50) }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $tour->packages_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">{{ $tour->session_templates_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning">{{ $tour->tour_sessions_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $tour->educators_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if($tour->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('panel.tours.edit', $tour->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('panel.tours.toggle', $tour->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-{{ $tour->is_active ? 'success' : 'secondary' }}"
                                                title="{{ $tour->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $tour->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        @if($tour->packages_count == 0 && $tour->tour_sessions_count == 0)
                                            <form action="{{ route('panel.tours.destroy', $tour->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this tour?')">
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#toursTable').DataTable({
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [6] },
                    { "searchable": false, "targets": [6] }
                ],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "emptyTable": "No tours found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ tours",
                    "infoEmpty": "Showing 0 of 0 tours",
                    "lengthMenu": "Show _MENU_ tours per page",
                    "search": "Search tour:",
                    "zeroRecords": "No matching tours found"
                }
            });
        });
    </script>
@endpush