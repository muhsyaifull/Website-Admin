@extends('layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_code)

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye text-primary"></i> Booking Details
        </h1>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Booking Information -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Booking Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold">Booking Code:</td>
                                    <td>{{ $booking->booking_code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Package:</td>
                                    <td>
                                        <span class="badge"
                                            style="background: {{ $booking->package->color }}; color: white;">
                                            {{ $booking->package->label }}
                                        </span>
                                        {{ $booking->package->name }}
                                        <br><small class="text-muted">{{ $booking->formatted_unit_price }} per
                                            person</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Visit Date:</td>
                                    <td>{{ $booking->formatted_visit_date }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Participants:</td>
                                    <td>
                                        <span class="badge badge-info">{{ $booking->total_participants }} total</span>
                                        <br>
                                        <small>{{ $booking->adult_count }} adults, {{ $booking->child_count }}
                                            children</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Total Price:</td>
                                    <td class="h5 text-success">{{ $booking->formatted_total_price }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold">Status:</td>
                                    <td>
                                        <span class="badge badge-lg"
                                            style="background: {{ $booking->status_color }}; color: white;">
                                            {{ $booking->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Created By:</td>
                                    <td>{{ $booking->user->name }} ({{ ucfirst($booking->user->role) }})</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Booking Time:</td>
                                    <td>{{ $booking->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                @if($booking->created_at != $booking->updated_at)
                                    <tr>
                                        <td class="font-weight-bold">Last Updated:</td>
                                        <td>{{ $booking->updated_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tour Schedule -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Tour Schedule</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="fas fa-seedling"></i> Tour Taman Atsiri
                            </h6>
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="font-weight-bold">{{ $booking->tamanSession->label }}</h6>
                                    <p class="text-muted mb-1">Guide: {{ $booking->tamanSession->educator->name }}</p>
                                    <small class="text-muted">
                                        Capacity:
                                        {{ $booking->tamanSession->booked }}/{{ $booking->tamanSession->capacity }}
                                        participants
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 style="color: #7B3F2A;">
                                <i class="fas fa-building"></i> Tour Museum Atsiri
                            </h6>
                            <div class="card" style="border-color: #7B3F2A;">
                                <div class="card-body">
                                    <h6 class="font-weight-bold">{{ $booking->museumSession->label }}</h6>
                                    <p class="text-muted mb-1">Guide: {{ $booking->museumSession->educator->name }}</p>
                                    <small class="text-muted">
                                        Capacity:
                                        {{ $booking->museumSession->booked }}/{{ $booking->museumSession->capacity }}
                                        participants
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Representative Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Representative Information</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">{{ $booking->representative_name }}</h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt"></i> {{ $booking->representative_address }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-phone"></i> {{ $booking->representative_phone }}
                    </p>
                </div>
            </div>

            <!-- Package Details -->
            <div class="card shadow">
                <div class="card-header py-3" style="background: {{ $booking->package->bg_color }};">
                    <h6 class="m-0 font-weight-bold" style="color: {{ $booking->package->color }};">Package Details</h6>
                </div>
                <div class="card-body">
                    <h6>{{ $booking->package->name }}</h6>
                    <p class="text-muted">{{ $booking->package->description }}</p>

                    <h6 class="mt-3">Included:</h6>
                    <ul class="list-unstyled">
                        @foreach($booking->package->includes as $include)
                            <li><i class="fas fa-check text-success"></i> {{ $include }}</li>
                        @endforeach
                    </ul>

                    @if($booking->package->has_saldo)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-credit-card"></i>
                            Saldo Voucher {{ $booking->package->formatted_saldo_amount }}
                        </div>
                    @endif

                    @if($booking->package->has_resto)
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-coffee"></i>
                            Refreshment Package Included
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection