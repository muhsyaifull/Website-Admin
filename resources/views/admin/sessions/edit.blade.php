@extends('layouts.app')

@section('title', 'Edit Session')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary"></i> Edit Session
        </h1>
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Sessions
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Session Information</h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.sessions.update', $session) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Tour Type</label>
                                    <input type="text" class="form-control" id="type"
                                        value="{{ $session->type == 'taman' ? 'Taman Atsiri' : 'Museum Atsiri' }}" readonly
                                        disabled>
                                    <small class="form-text text-muted">Tour type cannot be changed.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                                        name="date"
                                        value="{{ old('date', $session->date instanceof \Carbon\Carbon ? $session->date->format('Y-m-d') : $session->date) }}"
                                        required min="{{ date('Y-m-d') }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time" class="form-label">Start Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time"
                                        value="{{ old('start_time', \Carbon\Carbon::parse($session->start_time)->format('H:i')) }}"
                                        required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time" class="form-label">End Time <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time"
                                        value="{{ old('end_time', \Carbon\Carbon::parse($session->end_time)->format('H:i')) }}"
                                        required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="capacity" class="form-label">Capacity <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                        id="capacity" name="capacity" value="{{ old('capacity', $session->capacity) }}"
                                        required min="{{ $session->booked }}" max="50">
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($session->booked > 0)
                                        <small class="form-text text-muted">Minimum {{ $session->booked }} (already
                                            booked).</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="educator_id" class="form-label">Educator <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('educator_id') is-invalid @enderror" id="educator_id"
                                        name="educator_id" required>
                                        <option value="" disabled>Select educator</option>
                                        @foreach ($educators as $educator)
                                            <option value="{{ $educator->id }}" {{ old('educator_id', $session->educator_id) == $educator->id ? 'selected' : '' }}>
                                                {{ $educator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('educator_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active" class="form-label">Status</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            value="1" {{ old('is_active', $session->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($session->booked > 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> This session has <strong>{{ $session->booked }}</strong>
                                booking(s). Capacity cannot be set below the current number of bookings.
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Session
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection