@extends('layouts.app')

@section('title', 'Package Details - ' . $package->name)

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-box text-primary"></i> Package Details
        </h1>
        <div>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Package
            </a>
        </div>
    </div>

    <!-- Package Information -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Main Package Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background: {{ $package->bg_color }};">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold" style="color: {{ $package->color }};">
                            <span class="badge" style="background: {{ $package->color }}; color: white;">
                                {{ $package->label }}
                            </span>
                            {{ $package->name }}
                        </h6>
                        @if($package->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-warning">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Package Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold" width="40%">Price:</td>
                                    <td class="h5 text-success">{{ $package->formatted_price }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Status:</td>
                                    <td>
                                        @if($package->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Total Bookings:</td>
                                    <td>{{ $package->bookings->count() }} bookings</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Total Revenue:</td>
                                    <td class="text-success font-weight-bold">
                                        Rp {{ number_format($package->bookings->sum('total_price'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-info">Package Features</h6>
                            @if($package->has_saldo && $package->has_resto)
                                <div class="alert alert-primary">
                                    <i class="fas fa-star"></i> <strong>Full Package</strong>
                                    <br><small>Includes voucher and refreshment</small>
                                </div>
                            @elseif($package->has_saldo)
                                <div class="alert alert-info">
                                    <i class="fas fa-credit-card"></i> <strong>With Voucher</strong>
                                    <br><small>{{ $package->formatted_saldo_amount }} voucher included</small>
                                </div>
                            @elseif($package->has_resto)
                                <div class="alert alert-success">
                                    <i class="fas fa-coffee"></i> <strong>With Refreshment</strong>
                                    <br><small>Refreshment package included</small>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="fas fa-ticket-alt"></i> <strong>Basic Package</strong>
                                    <br><small>Tour only</small>
                                </div>
                            @endif

                            @if($package->has_saldo)
                                <div class="card border-primary mt-3">
                                    <div class="card-body py-2">
                                        <h6 class="text-primary mb-1">Voucher Information</h6>
                                        <div class="text-muted">
                                            Amount: <strong>{{ $package->formatted_saldo_amount }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary">Description</h6>
                            <p class="text-muted">{{ $package->description }}</p>
                        </div>
                    </div>

                    <!-- Includes -->
                    @if($package->includes && count($package->includes) > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-success">What's Included:</h6>
                                <div class="row">
                                    @foreach($package->includes as $include)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>{{ $include }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                </div>
                <div class="card-body">
                    @if($package->bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Booking Code</th>
                                        <th>Representative</th>
                                        <th>Participants</th>
                                        <th>Visit Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($package->bookings->take(10) as $booking)
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold">{{ $booking->booking_code }}</span>
                                                <br><small class="text-muted">{{ $booking->created_at->format('d M Y') }}</small>
                                            </td>
                                            <td>{{ $booking->representative_name }}</td>
                                            <td>{{ $booking->total_participants }} people</td>
                                            <td>{{ $booking->formatted_visit_date }}</td>
                                            <td class="text-success font-weight-bold">{{ $booking->formatted_total_price }}</td>
                                            <td>
                                                <span class="badge" style="background: {{ $booking->status_color }}; color: white;">
                                                    {{ $booking->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($package->bookings->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.bookings.index') }}?package={{ $package->id }}"
                                    class="btn btn-outline-primary">
                                    View All {{ $package->bookings->count() }} Bookings
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <div>No bookings for this package yet</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Package Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters">
                        <div class="col-12 mb-3">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Bookings</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $package->bookings->count() }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Revenue</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $package->formatted_revenue }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body py-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Participants</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $package->bookings->sum('total_participants') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Timeline -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Package Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Created</h6>
                                <p class="mb-0 text-muted">{{ $package->created_at->format('d M Y, H:i') }}</p>
                                <small class="text-muted">{{ $package->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @if($package->updated_at != $package->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Last Updated</h6>
                                    <p class="mb-0 text-muted">{{ $package->updated_at->format('d M Y, H:i') }}</p>
                                    <small class="text-muted">{{ $package->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif
                        @if($package->bookings->count() > 0)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">First Booking</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $package->bookings->first()->created_at->format('d M Y, H:i') }}
                                    </p>
                                    <small
                                        class="text-muted">{{ $package->bookings->first()->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e3e6f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .timeline-content {
            padding-left: 15px;
        }
    </style>
@endpush