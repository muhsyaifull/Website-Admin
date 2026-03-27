@extends('layouts.app')

@section('title', 'Reschedule Booking')

@section('content')
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center gap-2">
            <i class="fas fa-clock text-warning"></i>
            <span>Reschedule Booking</span>
        </h1>

        <a href="{{ route('kasir.index') }}" class="btn btn-secondary btn-sm d-flex align-items-center">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    {{-- Booking Info --}}
    <div class="card shadow mb-4">
        <div class="card-header py-2">
            <h6 class="m-0 font-weight-bold text-primary">Booking Information</h6>
        </div>

        <div class="card-body">
            <div class="row text-center text-md-left">
                <div class="col-md-3 mb-3 mb-md-0">
                    <small class="text-muted d-block">Booking Code</small>
                    <strong>{{ $booking->booking_code }}</strong>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <small class="text-muted d-block">Representative</small>
                    <strong>{{ $booking->representative_name }}</strong>
                </div>

                <div class="col-md-3 mb-3 mb-md-0">
                    <small class="text-muted d-block">Package</small>
                    <strong>{{ $booking->package->name }}</strong>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Participants</small>
                    <strong>{{ $booking->total_participants }} people</strong>
                    <small class="text-muted d-block">
                        {{ $booking->adult_count }} adults,
                        {{ $booking->child_count }} children
                        @if($booking->infant_count > 0)
                            , {{ $booking->infant_count }} infants
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <strong><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('kasir.booking.reschedule.store', $booking) }}" method="POST">
        @csrf

        <div class="row">
            @foreach($activeTours as $tour)
                @php
                    $sessions = ($tourSessions[$tour->id] ?? collect())
                        ->sortBy('start_time')
                        ->values();
                @endphp

                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        {{-- Header --}}
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">{{ $tour->name }}</h6>

                            @if(isset($currentSessionIds[$tour->id]))
                                <small class="text-muted text-right">
                                    Current:
                                    <strong>
                                        {{ $sessions->firstWhere('id', $currentSessionIds[$tour->id])?->label ?? '-' }}
                                    </strong>
                                </small>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="card-body p-3">
                            @if($sessions->isEmpty())
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-inbox mb-1"></i><br>
                                    No available sessions
                                </div>
                            @else
                                @foreach($sessions as $session)
                                    @php
                                        $isCurrent = isset($currentSessionIds[$tour->id]) && $currentSessionIds[$tour->id] == $session->id;
                                        $isExpired = Carbon\Carbon::parse($session->start_time)->subHour()->isPast();
                                        $available = $session->capacity - $session->booked;
                                        $isFull = $available < $booking->total_participants;
                                        $isDisabled = ($isExpired || $isFull) && !$isCurrent;
                                    @endphp

                                    <label class="d-flex align-items-start mb-2 p-3 border rounded session-option
                                        {{ $isCurrent ? 'border-warning' : '' }}
                                        {{ $isDisabled ? 'opacity-50' : '' }}"
                                        style="
                                            cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }};
                                            background: {{ $isCurrent ? '#FFF8E1' : ($isDisabled ? '#F5F5F5' : '#FFFCFA') }};
                                        ">

                                        {{-- Radio --}}
                                        <div class="d-flex align-items-center mr-3">
                                            <input type="radio"
                                                name="tour_session_{{ $tour->id }}"
                                                value="{{ $session->id }}"
                                                {{ $isCurrent ? 'checked' : '' }}
                                                {{ $isDisabled ? 'disabled' : '' }}
                                                required>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-grow-1">

                                            {{-- Top Row --}}
                                            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                                                <strong style="color:#4A2218;">
                                                    {{ $session->label }}
                                                </strong>

                                                <div class="d-flex align-items-center gap-1">
                                                    @if($isCurrent)
                                                        <span class="badge badge-warning mr-1">Current</span>
                                                    @endif

                                                    <span class="badge"
                                                        style="background: {{ $session->status_background }}; color: {{ $session->status_color }};">
                                                        {{ $session->status }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Progress --}}
                                            <div class="progress mb-2" style="height:6px;">
                                                <div class="progress-bar"
                                                    style="width: {{ $session->booking_percentage }}%; background-color: {{ $session->bar_color }};">
                                                </div>
                                            </div>

                                            {{-- Bottom Row --}}
                                            <div class="d-flex justify-content-between flex-wrap">
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i>
                                                    {{ $session->educator?->name ?? '-' }}
                                                </small>

                                                <small class="{{ $isFull && !$isCurrent ? 'text-danger' : 'text-muted' }}">
                                                    {{ $session->booked }}/{{ $session->capacity }}
                                                    @if($isFull && !$isCurrent)
                                                        <i class="fas fa-times-circle"></i>
                                                    @elseif($isExpired && !$isCurrent)
                                                        <i class="fas fa-times-circle"></i>
                                                    @else
                                                        ({{ $available }})
                                                    @endif
                                                </small>
                                            </div>

                                        </div>
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="card shadow">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                <div>
                    <i class="fas fa-info-circle text-info mr-1"></i>
                    <small class="text-muted">
                        Please select the new sessions for the booking. Sessions that are full or starting within 1 hour are not selectable.
                    </small>
                </div>

                <div class="gap-2">
                    <a href="{{ route('kasir.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-clock mr-1 "></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection