@extends('layouts.app')

@section('title', 'Edit Package - ' . $package->name)

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary"></i> Edit Package
        </h1>
        <div>
            <a href="{{ route('panel.packages.index') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-arrow-left"></i> Back to Packages
            </a>
            <a href="{{ route('panel.packages.show', $package->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Details
            </a>
        </div>
    </div>

    <!-- Package Edit Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Package Information</h6>
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

                    <form action="{{ route('panel.packages.update', $package->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name" class="form-label">Package Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $package->name) }}" required
                                        placeholder="Enter package name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="label" class="form-label">Package Label <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('label') is-invalid @enderror" id="label"
                                        name="label" value="{{ old('label', $package->label) }}" required
                                        placeholder="e.g., A, B, C">
                                    @error('label')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                name="description" rows="3" required
                                placeholder="Enter package description">{{ old('description', $package->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">Price (Rp) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                        id="price" name="price" value="{{ old('price', $package->price) }}" required min="0"
                                        step="1000" placeholder="0">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('is_active') is-invalid @enderror" id="is_active"
                                        name="is_active" required>
                                        <option value="1" {{ old('is_active', $package->is_active) == '1' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="0" {{ old('is_active', $package->is_active) == '0' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tour Selection -->
                        <div class="form-group mt-3">
                            <label class="form-label">Tours Included <span class="text-danger">*</span></label>
                            @php $selectedTourIds = old('tour_ids', $package->tours->pluck('id')->toArray()); @endphp
                            <div class="row">
                                @foreach($tours as $tour)
                                    <div class="col-md-4 mb-2">
                                        <div class="card border h-100">
                                            <div class="card-body py-2 px-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tour_ids[]"
                                                        value="{{ $tour->id }}" id="tour_{{ $tour->id }}"
                                                        {{ in_array($tour->id, $selectedTourIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tour_{{ $tour->id }}">
                                                        <strong>{{ $tour->name }}</strong>
                                                    </label>
                                                </div>
                                                @if($tour->description)
                                                    <small class="text-muted d-block ml-4">{{ Str::limit($tour->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('tour_ids')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Select which tours are included in this package</small>
                        </div>

                        <!-- Package Includes -->
                        <div class="form-group mt-4">
                            <label class="form-label">Package Includes</label>
                            <div id="includes_container">
                                @php
                                    $includes = old('includes', $package->includes ?? []);
                                @endphp
                                @if(!empty($includes))
                                    @foreach($includes as $index => $include)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="includes[]"
                                                placeholder="e.g., Guided tour" value="{{ $include }}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger" onclick="removeInclude(this)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="includes[]"
                                            placeholder="e.g., Guided tour">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeInclude(this)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm mt-2" onclick="addInclude()">
                                <i class="fas fa-plus"></i> Add Include
                            </button>
                            <small class="form-text text-muted">Add items included in this package</small>
                        </div>

                        <!-- Package Stats (if has bookings) -->
                        @if($package->bookings()->count() > 0)
                            <div class="alert alert-info mt-4">
                                <h6><i class="fas fa-info-circle"></i> Package Usage</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Total Bookings:</strong> {{ $package->bookings()->count() }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Revenue:</strong> Rp
                                        {{ number_format($package->bookings()->sum('total_price'), 0, ',', '.') }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Participants:</strong>
                                        {{ $package->bookings()->sum('total_participants') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('panel.packages.index') }}"
                                            class="btn btn-outline-secondary mr-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        <a href="{{ route('panel.packages.show', $package->id) }}"
                                            class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Update Package
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Price formatting
            $('#price').on('input', function () {
                let value = parseInt($(this).val()) || 0;
                $(this).next('.price-display').remove();
                $(this).after('<small class="price-display text-muted">Rp ' + value.toLocaleString('id-ID') + '</small>');
            });

            // Initial price display
            $('#price').trigger('input');
        });

        function addInclude() {
            const container = document.getElementById('includes_container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                    <input type="text" 
                           class="form-control" 
                           name="includes[]" 
                           placeholder="e.g., Entry ticket">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-danger" onclick="removeInclude(this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
            container.appendChild(div);
        }

        function removeInclude(button) {
            button.closest('.input-group').remove();
        }
    </script>
@endpush