@extends('layouts.app')

@section('title', 'Booking Confirmation')

@section('content')
    <div class="card shadow" style="max-width: 600px; margin: 0 auto;">
        <!-- Header -->
        <div class="card-header text-white text-center" style="background: #4A2218;">
            <div style="font-size: 56px; margin-bottom: 8px;">✅</div>
            <div style="font-weight: 700; font-size: 20px; color: #1E8449; margin-bottom: 4px;">Booking Successful!</div>
            <div style="color: #C0A090; font-size: 14px;">Print schedule confirmation for visitor</div>
        </div>

        <!-- Booking Confirmation Ticket -->
        <div class="card-body text-center">
            <div class="border border-2 rounded mx-auto p-4 mb-4"
                style="border-style: dashed!important; border-color: #D0B8AD!important; background: #FFFCFA; max-width: 400px;">
                <div class="text-center font-weight-bold mb-2" style="color: #4A2218;">RUMAH ATSIRI INDONESIA</div>
                <div class="text-muted text-center mb-3" style="font-size: 12px;">Tour Schedule Confirmation</div>

                <div class="text-left" style="font-size: 13px;">
                    <div class="mb-2"><strong>Booking Code:</strong> {{ $booking->booking_code }}</div>
                    <div class="mb-2"><strong>Package:</strong> {{ $booking->package->name }}</div>
                    <div class="mb-2"><strong>Participants:</strong> {{ $booking->total_participants }} people
                        <small class="text-muted">({{ $booking->adult_count }} adults, {{ $booking->child_count }}
                            children)</small>
                    </div>
                    @if(count($tourSessionMap) > 0)
                        @foreach($tourSessionMap as $tourId => $session)
                            <div class="mb-2">
                                <strong>
                                    {{ $session->tour->name }}:</strong>
                                {{ $session->label }}
                            </div>
                        @endforeach
                    @else
                        <div class="mb-2 text-muted">No sessions assigned</div>
                    @endif
                    <div class="mb-2"><strong>Visit Date:</strong> {{ $booking->formatted_visit_date }}</div>
                    <div class="mb-3"><strong>Total Payment:</strong> <span
                            class="text-success font-weight-bold">{{ $booking->formatted_total_price }}</span></div>

                    <hr style="border-top: 1px dashed #D0B8AD;">
                    <div class="text-muted text-center" style="font-size: 11px; padding-top: 8px;">
                        Arrive 10 minutes before the schedule at the main gathering point.
                    </div>
                </div>
            </div>

            <!-- Detailed Booking Information -->
            <div class="card border-0" style="background: #F8F9FA;">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">Representative Details</h6>
                    <div class="text-left">
                        <p class="mb-2"><strong>Name:</strong> {{ $booking->representative_name }}</p>
                        <p class="mb-2"><strong>Address:</strong> {{ $booking->representative_address }}</p>
                        <p class="mb-2"><strong>Phone:</strong> {{ $booking->representative_phone }}</p>
                        <p class="mb-0"><strong>Created by:</strong> {{ $booking->user->name }} •
                            {{ $booking->created_at->format('H:i, d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4">
                <button onclick="window.print()" class="btn btn-primary mr-2">
                    <i class="fas fa-print"></i> Print Ticket
                </button>
                <a href="{{ route('kasir.booking.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> New Transaction
                </a>
                <a href="{{ route('kasir.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        // Auto redirect to new booking after 30 seconds (optional)
        setTimeout(function () {
            if (confirm('Create new booking?')) {
                window.location.href = "{{ route('kasir.booking.create') }}";
            }
        }, 30000);
    </script>
@endpush