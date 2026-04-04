@extends('layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_code)

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye text-primary"></i> Reservation Details
        </h1>
        <a href="{{ route('panel.bookings.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <!-- Booking Information -->
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
                                    <td>
                                        <span style="font-family: monospace; font-size: 15px; font-weight: 700;">
                                            {{ $booking->booking_code }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Package:</td>
                                    <td>
                                        <span class="badge badge-primary" style="color: white;">
                                            {{ $booking->package->label }}
                                        </span>
                                        {{ $booking->package->name }}
                                        <br><small class="text-muted">{{ $booking->formatted_unit_price }} / person</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Visit Date:</td>
                                    <td>{{ $booking->formatted_visit_date }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Visitor Type:</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $booking->visitor_type_label }}</span>
                                    </td>
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
                                    <td>{{ $booking->created_at->format('H:i, d M Y') }}</td>
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

            <!-- Participants & Pricing -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Participants & Pricing</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border rounded p-3" style="background: #F7F0EC;">
                                <div style="font-size: 28px; font-weight: 700; color: #3A1A0E; font-family: monospace;">
                                    {{ $booking->adult_count }}
                                </div>
                                <div class="text-muted"
                                    style="font-size: 11px; text-transform: uppercase; letter-spacing: .5px;">Adults</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3" style="background: #F7F0EC;">
                                <div style="font-size: 28px; font-weight: 700; color: #3A1A0E; font-family: monospace;">
                                    {{ $booking->child_count }}
                                </div>
                                <div class="text-muted"
                                    style="font-size: 11px; text-transform: uppercase; letter-spacing: .5px;">Children</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3" style="background: #F7F0EC;">
                                <div style="font-size: 28px; font-weight: 700; color: #3A1A0E; font-family: monospace;">
                                    {{ $booking->infant_count }}
                                </div>
                                <div class="text-muted"
                                    style="font-size: 11px; text-transform: uppercase; letter-spacing: .5px;">Infants</div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Total Participants (capacity)</td>
                            <td class="font-weight-bold">{{ $booking->total_participants }} people</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Paid Participants</td>
                            <td>
                                {{ $booking->adult_count + $booking->child_count }} people
                                <small class="text-muted">(adults + children, infants (free))</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Unit Price</td>
                            <td>{{ $booking->formatted_unit_price }}</td>
                        </tr>
                        <tr style="border-top: 2px solid #E0CEC6;">
                            <td class="font-weight-bold">Total Payment</td>
                            <td class="h5 text-success font-weight-bold mb-0">{{ $booking->formatted_total_price }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Tour Schedule -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Tour Schedule</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($booking->bookingSessions && $booking->bookingSessions->count() > 0)
                            @foreach($booking->bookingSessions as $session)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary">
                                        {{ $session->tour->name ?? 'Tour' }}
                                    </h6>
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold">{{ $session->label }}</h6>
                                            <p class="text-muted mb-1">
                                                Guide: {{ $session->educator ? $session->educator->name : '-' }}
                                            </p>
                                            <small class="text-muted">
                                                Capacity: {{ $session->booked }}/{{ $session->capacity }} participants
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <p class="text-muted">No sessions assigned</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <!-- Representative Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Guest Information</h6>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Package Details</h6>
                </div>
                <div class="card-body">
                    <h6>Tour:</h6>
                    <h6">- {{ $booking->package->name }}</h6>
                        <p class="text-muted">{{ $booking->package->description }}</p>

                        <h6 class="mt-3">Included:</h6>
                        <ul class="list-unstyled">
                            @foreach($booking->package->includes as $include)
                                <li><i class="fas fa-check text-success"></i> {{ $include }}</li>
                            @endforeach
                        </ul>
                </div>
            </div>

        </div>
    </div>

@endsection