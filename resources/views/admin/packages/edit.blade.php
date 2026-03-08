@extends('layouts.app')

@section('title', 'Edit Package - ' . $package->name)

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary"></i> Edit Package
        </h1>
        <div>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-primary mr-2">
                <i class="fas fa-arrow-left"></i> Back to Packages
            </a>
            <a href="{{ route('admin.packages.show', $package->id) }}" class="btn btn-info">
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

                    <form action="{{ route('admin.packages.update', $package->id) }}" method="POST">
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

                        <!-- Colors -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color" class="form-label">Badge Color <span
                                            class="text-danger">*</span></label>
                                    <input type="color" class="form-control @error('color') is-invalid @enderror" id="color"
                                        name="color" value="{{ old('color', $package->color) }}" required>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Color for package badge</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bg_color" class="form-label">Background Color <span
                                            class="text-danger">*</span></label>
                                    <input type="color" class="form-control @error('bg_color') is-invalid @enderror"
                                        id="bg_color" name="bg_color" value="{{ old('bg_color', $package->bg_color) }}"
                                        required>
                                    @error('bg_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Background color for package cards</small>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Features -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Voucher Saldo</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="has_saldo" id="has_saldo"
                                                value="1" {{ old('has_saldo', $package->has_saldo) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_saldo">
                                                Include Voucher Saldo
                                            </label>
                                        </div>
                                        <div class="form-group mt-3" id="saldo_amount_group"
                                            style="display: {{ $package->has_saldo ? 'block' : 'none' }};">
                                            <label for="saldo_amount" class="form-label">Saldo Amount (Rp)</label>
                                            <input type="number"
                                                class="form-control @error('saldo_amount') is-invalid @enderror"
                                                id="saldo_amount" name="saldo_amount"
                                                value="{{ old('saldo_amount', $package->saldo_amount) }}" min="0"
                                                step="1000" placeholder="0">
                                            @error('saldo_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Refreshment Package</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="has_resto" id="has_resto"
                                                value="1" {{ old('has_resto', $package->has_resto) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_resto">
                                                Include Refreshment Package
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            Includes snacks and beverages for participants
                                        </small>
                                    </div>
                                </div>
                            </div>
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
                                        <a href="{{ route('admin.packages.index') }}"
                                            class="btn btn-outline-secondary mr-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                        <a href="{{ route('admin.packages.show', $package->id) }}"
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
            // Toggle saldo amount field
            $('#has_saldo').change(function () {
                if ($(this).is(':checked')) {
                    $('#saldo_amount_group').show();
                    $('#saldo_amount').attr('required', true);
                } else {
                    $('#saldo_amount_group').hide();
                    $('#saldo_amount').attr('required', false);
                    $('#saldo_amount').val('');
                }
            });

            // Set initial state
            if ($('#has_saldo').is(':checked')) {
                $('#saldo_amount').attr('required', true);
            }

            // Price formatting
            $('#price, #saldo_amount').on('input', function () {
                let value = parseInt($(this).val()) || 0;
                $(this).next('.price-display').remove();
                $(this).after('<small class="price-display text-muted">Rp ' + value.toLocaleString('id-ID') + '</small>');
            });

            // Initial price display
            $('#price, #saldo_amount').trigger('input');
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