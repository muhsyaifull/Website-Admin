@extends('layouts.app')

@section('title', 'Educator Detail')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chalkboard-teacher text-primary"></i> {{ $educator->name }}
        </h1>
        <div>
            <a href="{{ route('panel.educators.edit', $educator) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('panel.educators.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Educator Info -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Educator Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Name</th>
                            <td>{{ $educator->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Phone</th>
                            <td>{{ $educator->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Specialization</th>
                            <td>
                                @if($educator->tours && $educator->tours->count() > 0)
                                    @foreach($educator->tours as $tour)
                                        <span class="badge badge-primary">{{ $tour->name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-secondary">No specialization</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td>
                                @if($educator->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Today's Sessions -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-calendar-day"></i> Today's Sessions
                        <span class="badge badge-success ml-1">{{ $todaySessions->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($todaySessions->isEmpty())
                        <p class="text-muted text-center my-3">No sessions today.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Type</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySessions as $session)
                                        <tr>
                                            <td><span class="badge badge-dark">{{ $session->label }}</span></td>
                                            <td>
                                                @if($session->tour)
                                                    <span class="badge badge-primary" style="color: white;">
                                                        {{ $session->tour->name }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $session->type }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $session->booked }}/{{ $session->capacity }}</td>
                                            <td>
                                                @if($session->is_full)
                                                    <span class="badge badge-danger">Full</span>
                                                @elseif(!$session->is_active)
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @else
                                                    <span class="badge badge-success">Available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    @if($upcomingSessions->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-calendar-alt"></i> Upcoming Sessions
                    <span class="badge badge-info ml-1">{{ $upcomingSessions->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingSessions as $session)
                                <tr>
                                    <td>{{ $session->date->format('d M Y') }} <small
                                            class="text-muted">({{ $session->date->translatedFormat('l') }})</small></td>
                                    <td><span class="badge badge-dark">{{ $session->label }}</span></td>
                                    <td>
                                        @if($session->tour)
                                            <span class="badge badge-primary" style="color: white;">
                                                {{ $session->tour->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">{{ $session->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $session->booked }}/{{ $session->capacity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection