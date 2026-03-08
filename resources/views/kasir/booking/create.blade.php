@extends('layouts.app')

@section('title', 'Create Tour Booking')

@section('content')
    <div class="card shadow" style="max-width: 800px; margin: 0 auto;">
        <!-- Header -->
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: #4A2218;">
            <div>
                <div style="font-size: 11px; color: #C0A090; letter-spacing: 1px;">RUMAH ATSIRI INDONESIA</div>
                <div style="font-weight: 700; font-size: 17px;">🏪 Cashier System — Tour Scheduling</div>
            </div>
            <div style="text-align: right; font-size: 12px; color: #C0A090;">
                <div>Cashier: {{ auth()->user()->name }}</div>
                <div>{{ Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</div>
            </div>
        </div>

        <!-- Step Progress Bar -->
        <div style="background: #F5EDE8; padding: 10px 20px;">
            <div class="d-flex">
                <div class="flex-fill d-flex align-items-center">
                    <div class="d-flex align-items-center mr-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                            style="width: 26px; height: 26px; background: #7B3F2A; font-size: 12px;">1</div>
                        <span class="ml-2 font-weight-bold" style="font-size: 12px; color: #4A2218;">Select Package</span>
                    </div>
                    <div class="flex-fill" style="height: 2px; background: #D0B8AD; margin: 0 8px;"></div>
                </div>
                <div class="flex-fill d-flex align-items-center">
                    <div class="d-flex align-items-center mr-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center font-weight-bold"
                            style="width: 26px; height: 26px; background: #D0B8AD; color: #999; font-size: 12px;">2</div>
                        <span class="ml-2" style="font-size: 12px; color: #AAA;">Participants</span>
                    </div>
                    <div class="flex-fill" style="height: 2px; background: #D0B8AD; margin: 0 8px;"></div>
                </div>
                <div class="flex-fill d-flex align-items-center">
                    <div class="d-flex align-items-center mr-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center font-weight-bold"
                            style="width: 26px; height: 26px; background: #D0B8AD; color: #999; font-size: 12px;">3</div>
                        <span class="ml-2" style="font-size: 12px; color: #AAA;">Representative Info</span>
                    </div>
                    <div class="flex-fill" style="height: 2px; background: #D0B8AD; margin: 0 8px;"></div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center font-weight-bold"
                            style="width: 26px; height: 26px; background: #D0B8AD; color: #999; font-size: 12px;">4</div>
                        <span class="ml-2" style="font-size: 12px; color: #AAA;">Select Schedule</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form id="bookingForm" action="{{ route('kasir.booking.store') }}" method="POST">
                @csrf

                <!-- Step 1: Select Package -->
                <div class="booking-step" id="step1">
                    <h5 class="font-weight-bold mb-3" style="color: #4A2218;">Select Bundling Package</h5>
                    <div class="row">
                        @foreach($packages as $package)
                            <div class="col-12 mb-3">
                                <div class="package-option border rounded p-3" data-package="{{ $package->id }}"
                                    style="cursor: pointer; border-color: #D0B8AD!important; background: #FFFCFA;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge text-white font-weight-bold mr-2"
                                                style="background: {{ $package->color }}; padding: 2px 10px; font-size: 12px;">{{ $package->label }}</span>
                                            <span class="font-weight-bold"
                                                style="color: #4A2218; font-size: 15px;">{{ $package->name }}</span>
                                        </div>
                                        <span class="font-weight-bold"
                                            style="color: {{ $package->color }}; font-size: 16px;">{{ $package->formatted_price }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap mt-2">
                                        @foreach($package->includes as $item)
                                            <span class="badge mr-1 mb-1"
                                                style="background: {{ $package->bg_color }}; color: {{ $package->color }}; border: 1px solid {{ $package->color }}50; padding: 2px 10px; font-size: 12px;">
                                                ✓ {{ $item }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="package_id" id="selectedPackage">
                </div>

                <!-- Step 2: Participants -->
                <div class="booking-step" id="step2" style="display: none;">
                    <h5 class="font-weight-bold mb-3" style="color: #4A2218;">Number of Participants</h5>
                    <div id="selectedPackageInfo" class="mb-3"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Adult Participants <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex align-items-center justify-content-center my-3">
                                    <button type="button" class="btn btn-outline-primary rounded-circle" id="adultMinus"
                                        style="width: 44px; height: 44px; border: 2px solid #7B3F2A; color: #7B3F2A;">−</button>
                                    <div class="mx-4 text-center">
                                        <div class="h2 font-weight-bold mb-0" style="color: #4A2218;" id="adultDisplay">1
                                        </div>
                                        <small class="text-muted">adults</small>
                                    </div>
                                    <button type="button" class="btn text-white rounded-circle" id="adultPlus"
                                        style="width: 44px; height: 44px; border: 2px solid #7B3F2A; background: #7B3F2A;">+</button>
                                </div>
                                <input type="hidden" name="adult_count" id="adultCount" value="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Children Participants <small
                                        class="text-muted">(Gratis)</small></label>
                                <div class="d-flex align-items-center justify-content-center my-3">
                                    <button type="button" class="btn btn-outline-secondary rounded-circle" id="childMinus"
                                        style="width: 44px; height: 44px;">−</button>
                                    <div class="mx-4 text-center">
                                        <div class="h2 font-weight-bold mb-0" style="color: #4A2218;" id="childDisplay">0
                                        </div>
                                        <small class="text-muted">children</small>
                                    </div>
                                    <button type="button" class="btn btn-secondary rounded-circle" id="childPlus"
                                        style="width: 44px; height: 44px;">+</button>
                                </div>
                                <input type="hidden" name="child_count" id="childCount" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded text-center font-weight-bold" style="background: #F5EDE8; color: #7B3F2A;"
                        id="priceCalculation">
                        Total: 1 × Rp 75.000 = Rp 75.000
                    </div>
                </div>

                <!-- Step 3: Representative Info -->
                <div class="booking-step" id="step3" style="display: none;">
                    <h5 class="font-weight-bold mb-3" style="color: #4A2218;">Representative Information</h5>

                    <div class="form-group">
                        <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="representative_name" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Full Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="representative_address" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="representative_phone" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Visit Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="visit_date" min="{{ date('Y-m-d') }}"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Step 4: Select Schedule -->
                <div class="booking-step" id="step4" style="display: none;">
                    <h5 class="font-weight-bold mb-3" style="color: #4A2218;">Select Tour Schedule</h5>
                    <div id="participantInfo" class="mb-3"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success mb-3">
                                <i class="fas fa-seedling"></i> Tour Taman Atsiri
                                <span id="selectedTamanInfo" class="badge badge-success ml-2" style="display: none;"></span>
                            </h6>
                            <div id="tamanSessions">
                                @foreach($tamanSessions as $session)
                                    <div class="session-option mb-2 border rounded p-2" data-session="{{ $session->id }}"
                                        data-type="taman" data-capacity="{{ $session->capacity }}"
                                        data-booked="{{ $session->booked }}"
                                        style="cursor: pointer; border-color: #D0B8AD!important; background: #FFFCFA;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong style="color: #4A2218;">{{ $session->label }}</strong>
                                            <small class="text-muted">Guide: {{ $session->educator->name }}</small>
                                        </div>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar"
                                                style="width: {{ $session->booking_percentage }}%; background: {{ $session->bar_color }};">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $session->booked }}/{{ $session->capacity }}
                                                participants</small>
                                            <span class="badge"
                                                style="background: {{ $session->status_background }}; color: {{ $session->status_color }}; font-size: 11px;">
                                                {{ $session->status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="taman_session_id" id="tamanSessionId">
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold mb-3" style="color: #7B3F2A;">
                                <i class="fas fa-building"></i> Tour Museum Atsiri
                                <span id="selectedMuseumInfo" class="badge ml-2"
                                    style="background: #7B3F2A; display: none;"></span>
                            </h6>
                            <div id="museumSessions">
                                @foreach($museumSessions as $session)
                                    <div class="session-option mb-2 border rounded p-2" data-session="{{ $session->id }}"
                                        data-type="museum" data-capacity="{{ $session->capacity }}"
                                        data-booked="{{ $session->booked }}"
                                        style="cursor: pointer; border-color: #D0B8AD!important; background: #FFFCFA;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong style="color: #4A2218;">{{ $session->label }}</strong>
                                            <small class="text-muted">Guide: {{ $session->educator->name }}</small>
                                        </div>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar"
                                                style="width: {{ $session->booking_percentage }}%; background: {{ $session->bar_color }};">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $session->booked }}/{{ $session->capacity }}
                                                participants</small>
                                            <span class="badge"
                                                style="background: {{ $session->status_background }}; color: {{ $session->status_color }}; font-size: 11px;">
                                                {{ $session->status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="museum_session_id" id="museumSessionId">
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 1.5px solid #EDE0D8;">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)"
                        style="border-color: #DDD;" disabled>
                        ← Back
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)"
                        style="background: #7B3F2A; border-color: #7B3F2A;" disabled>
                        Next →
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        ✓ Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let currentStep = 1;
        let selectedPackage = null;
        let adultCount = 1;
        let childCount = 0;

        // Package selection
        $(document).on('click', '.package-option', function () {
            $('.package-option').removeClass('selected').css({
                'border-color': '#D0B8AD',
                'background': '#FFFCFA'
            });

            $(this).addClass('selected').css({
                'border-color': '#7B3F2A',
                'border-width': '2.5px',
                'background': '#F5EDE8'
            });

            selectedPackage = $(this).data('package');
            $('#selectedPackage').val(selectedPackage);
            $('#nextBtn').prop('disabled', false);
        });

        // Session selection
        $(document).on('click', '.session-option', function () {
            const type = $(this).data('type');
            const sessionId = $(this).data('session');
            const capacity = $(this).data('capacity');
            const booked = $(this).data('booked');
            const available = capacity - booked;
            const totalParticipants = adultCount + childCount;

            if (available < totalParticipants) {
                alert('Session capacity is insufficient for the selected number of participants!');
                return;
            }

            // Remove selection from same type
            $(`[data-type="${type}"]`).removeClass('selected').css({
                'border-color': '#D0B8AD',
                'background': '#FFFCFA'
            });

            // Select this session
            $(this).addClass('selected').css({
                'border-color': '#7B3F2A',
                'border-width': '2.5px',
                'background': '#F5EDE8'
            });

            if (type === 'taman') {
                $('#tamanSessionId').val(sessionId);
                $('#selectedTamanInfo').text($(this).find('strong').text()).show();
            } else {
                $('#museumSessionId').val(sessionId);
                $('#selectedMuseumInfo').text($(this).find('strong').text()).show();
            }

            // Check if both sessions are selected
            if ($('#tamanSessionId').val() && $('#museumSessionId').val()) {
                $('#nextBtn').prop('disabled', false);
            }
        });

        // Quantity controls
        $('#adultPlus').click(function () {
            if (adultCount < 20) {
                adultCount++;
                updateQuantityDisplay();
            }
        });

        $('#adultMinus').click(function () {
            if (adultCount > 1) {
                adultCount--;
                updateQuantityDisplay();
            }
        });

        $('#childPlus').click(function () {
            if (childCount < 10) {
                childCount++;
                updateQuantityDisplay();
            }
        });

        $('#childMinus').click(function () {
            if (childCount > 0) {
                childCount--;
                updateQuantityDisplay();
            }
        });

        function updateQuantityDisplay() {
            $('#adultDisplay').text(adultCount);
            $('#childDisplay').text(childCount);
            $('#adultCount').val(adultCount);
            $('#childCount').val(childCount);

            if (selectedPackage) {
                // Update price calculation
                const packages = @json($packages);
                const pkg = packages.find(p => p.id == selectedPackage);
                const total = pkg.price * adultCount;
                $('#priceCalculation').html(`Total: ${adultCount} × Rp ${pkg.price.toLocaleString('id-ID')} = <span style="font-size: 16px;">Rp ${total.toLocaleString('id-ID')}</span>`);
            }
        }

        function changeStep(direction) {
            if (direction === 1) {
                // Validation before proceeding
                if (currentStep === 1 && !selectedPackage) {
                    alert('Please select a bundling package first');
                    return;
                }
                if (currentStep === 3) {
                    // Validate form fields
                    const name = $('[name="representative_name"]').val();
                    const address = $('[name="representative_address"]').val();
                    const phone = $('[name="representative_phone"]').val();
                    const visitDate = $('[name="visit_date"]').val();

                    if (!name || !address || !phone || !visitDate) {
                        alert('Please complete all representative information');
                        return;
                    }
                }
            }

            currentStep += direction;

            // Hide all steps
            $('.booking-step').hide();

            // Show current step
            $(`#step${currentStep}`).show();

            // Update step progress bar
            updateStepProgress();

            // Handle specific step logic
            if (currentStep === 2 && direction === 1) {
                // Show selected package info
                const packages = @json($packages);
                const pkg = packages.find(p => p.id == selectedPackage);
                $('#selectedPackageInfo').html(`
                        <div class="text-muted">Selected package: <strong style="color: #7B3F2A;">${pkg.label} — ${pkg.name}</strong></div>
                    `);
                updateQuantityDisplay();
                $('#nextBtn').prop('disabled', false);
            }

            if (currentStep === 4 && direction === 1) {
                // Show participant info
                $('#participantInfo').html(`
                        <div class="text-muted">${adultCount + childCount} participants • ${selectedPackage ? packages.find(p => p.id == selectedPackage).name : ''}</div>
                    `);
                $('#nextBtn').prop('disabled', true);
            }

            // Update buttons
            $('#prevBtn').prop('disabled', currentStep === 1);

            if (currentStep === 4) {
                $('#nextBtn').hide();
                $('#submitBtn').show().prop('disabled', !$('#tamanSessionId').val() || !$('#museumSessionId').val());
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();

                if (currentStep === 1) {
                    $('#nextBtn').prop('disabled', !selectedPackage);
                } else if (currentStep === 2) {
                    $('#nextBtn').prop('disabled', false);
                } else if (currentStep === 3) {
                    $('#nextBtn').prop('disabled', false);
                }
            }
        }

        function updateStepProgress() {
            $('.rounded-circle').each(function (index) {
                const stepNum = index + 1;
                const $circle = $(this);
                const $text = $circle.next();

                if (stepNum < currentStep) {
                    $circle.css({ 'background': '#7B3F2A', 'color': '#fff' }).text('✓');
                    $text.css({ 'color': '#4A2218', 'font-weight': '700' });
                } else if (stepNum === currentStep) {
                    $circle.css({ 'background': '#C0622A', 'color': '#fff' }).text(stepNum);
                    $text.css({ 'color': '#4A2218', 'font-weight': '700' });
                } else {
                    $circle.css({ 'background': '#D0B8AD', 'color': '#999' }).text(stepNum);
                    $text.css({ 'color': '#AAA', 'font-weight': '400' });
                }
            });
        }

        // Form validation
        $('#bookingForm').submit(function (e) {
            if (!$('#tamanSessionId').val() || !$('#museumSessionId').val()) {
                e.preventDefault();
                alert('Please select schedules for both tours (Taman and Museum)');
            }
        });
    </script>
@endpush