@extends('layouts.app')

@section('title', 'Add Session')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Add Session
        </h1>
        <a href="{{ route('panel.sessions.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Sessions
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
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

                    <form action="{{ route('panel.sessions.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                                    <select class="form-control @error('tour_id') is-invalid @enderror" id="tour_id"
                                        name="tour_id" required>
                                        <option value="" disabled {{ old('tour_id') ? '' : 'selected' }}>Select tour
                                        </option>
                                        @foreach($tours as $tour)
                                            <option value="{{ $tour->id }}" {{ old('tour_id') == $tour->id ? 'selected' : '' }}>
                                                {{ $tour->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tour_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                                        name="date" value="{{ old('date', date('Y-m-d')) }}" required
                                        min="{{ date('Y-m-d') }}">
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
                                        id="start_time" name="start_time" value="{{ old('start_time') }}" required>
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
                                        id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="capacity" class="form-label">Capacity <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                        id="capacity" name="capacity" value="{{ old('capacity', 20) }}" required min="1"
                                        max="50">
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="educator_id" class="form-label">Educator <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('educator_id') is-invalid @enderror" id="educator_id"
                                        name="educator_id" required>
                                        <option value="" disabled {{ old('educator_id') ? '' : 'selected' }}>Select educator
                                        </option>
                                        @foreach($educators as $educator)
                                            <option value="{{ $educator->id }}" {{ old('educator_id') == $educator->id ? 'selected' : '' }}>
                                                {{ $educator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('educator_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            This session will be created manually and is <strong>not linked to any template</strong>.
                            It won't be affected by template changes.
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('panel.sessions.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Session
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection