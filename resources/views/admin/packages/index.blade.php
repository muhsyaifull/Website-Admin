@extends('layouts.app')

@section('title', 'Package Management')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-box text-primary"></i> Package Management
        </h1>
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Package
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

    <!-- Package Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Packages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $packages->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Inactive Packages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $packages->where('is_active', false)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
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
                                Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $packages->sum('bookings_count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRevenue }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Package List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Package List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="packagesTable" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>Package</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge"
                                            style="background: {{ $package->color }}; color: white; margin-right: 8px;">
                                            {{ $package->label }}
                                        </span>
                                        <div>
                                            <div class="font-weight-bold">{{ $package->name }}</div>
                                            <small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($package->has_saldo && $package->has_resto)
                                        <span class="badge badge-primary">Full Package</span>
                                    @elseif($package->has_saldo)
                                        <span class="badge badge-info">With Voucher</span>
                                    @elseif($package->has_resto)
                                        <span class="badge badge-success">With Refreshment</span>
                                    @else
                                        <span class="badge badge-secondary">Basic</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $package->formatted_price }}</div>
                                    @if($package->has_saldo)
                                        <small class="text-primary">+ {{ $package->formatted_saldo_amount }} voucher</small>
                                    @endif
                                </td>
                                <td>
                                    @if($package->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $package->bookings_count }}</div>
                                    @if($package->bookings_count > 0)
                                        <small class="text-success">{{ $package->formatted_revenue }} revenue</small>
                                    @endif
                                </td>
                                <td>
                                    @if($package->updated_at == $package->created_at)
                                        <small>Created {{ $package->created_at->diffForHumans() }}</small>
                                    @else
                                        <small>Updated {{ $package->updated_at->diffForHumans() }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.packages.show', $package->id) }}"
                                            class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.packages.edit', $package->id) }}"
                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this package?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                @if($package->bookings_count > 0) disabled @endif>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
            $('#packagesTable').DataTable({
                "order": [[5, "desc"]], 
                "columnDefs": [
                    { "orderable": false, "targets": [6] }, 
                    { "searchable": false, "targets": [6] }  
                ],
                "pageLength": 25,
                "responsive": true,
                "language": {
                    "emptyTable": "No packages found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ packages",
                    "infoEmpty": "Showing 0 to 0 of 0 packages",
                    "lengthMenu": "Show _MENU_ packages per page",
                    "search": "Search packages:",
                    "zeroRecords": "No matching packages found"
                }
            });
        });
    </script>
@endpush