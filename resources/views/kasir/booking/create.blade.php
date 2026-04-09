@extends('layouts.app')

@section('title', 'Create Tour Booking')

@push('styles')
    <link href="{{ asset('css/kasir-booking-create.css') }}" rel="stylesheet">
@endpush

@section('content')

    <div class="booking-wrap">
        <div class="bk-header">
            <div>
                <div class="bk-header-brand">Rumah Atsiri Indonesia</div>
                <div class="bk-header-title">Cashier — Tour Reservation</div>
            </div>
            <div class="bk-header-meta">
                <div>{{ auth()->user()->name }}</div>
                <div>{{ Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</div>
            </div>
        </div>

        <!-- Steps -->
        <div class="bk-steps">
            <div class="bk-step active" id="stepTab1">
                <div class="bk-step-num">1</div>
                <div class="bk-step-label">Package</div>
            </div>
            <div class="bk-step" id="stepTab2">
                <div class="bk-step-num">2</div>
                <div class="bk-step-label">Participants</div>
            </div>
            <div class="bk-step" id="stepTab3">
                <div class="bk-step-num">3</div>
                <div class="bk-step-label">Guest</div>
            </div>
            <div class="bk-step" id="stepTab4">
                <div class="bk-step-num">4</div>
                <div class="bk-step-label">Schedule</div>
            </div>
        </div>

        <div class="bk-body">

            @if($errors->any())
                <div class="bk-alert-error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="server-error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="bookingForm" action="{{ route('kasir.booking.store') }}" method="POST">
                @csrf

                <!-- Step 1: Package -->
                <div class="booking-step" id="step1">
                    <div class="bk-section-title">Select Package</div>
                    <div class="pkg-grid">
                        @foreach($packages as $package)
                            <div class="pkg-card" data-package="{{ $package->id }}">
                                <div class="pkg-card-radio"></div>
                                <div class="pkg-card-info">
                                    <div class="pkg-card-name">
                                        <span class="pkg-card-label">{{ $package->label }}</span>
                                        {{ $package->name }}
                                    </div>

                                    <div class="pkg-sub">
                                        <div class="pkg-sub-title">Tours:</div>
                                        <div class="pkg-card-tours">
                                            @foreach($package->tours->where('is_active', true) as $tour)
                                                <span class="pkg-tag2">{{ $tour->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="pkg-sub">
                                        <div class="pkg-sub-title">Includes:</div>
                                        <div class="pkg-card-includes">
                                            @foreach($package->includes as $item)
                                                <span class="pkg-tag">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="pkg-card-price">{{ $package->formatted_price }}</div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="package_id" id="selectedPackage">
                </div>

                <!-- Step 2: Participants -->
                <div class="booking-step step-hidden" id="step2">
                    <div class="bk-section-title">Participants</div>
                    <div id="selectedPackageInfo"></div>

                    <div class="qty-row">
                        <div class="qty-block">
                            <div class="qty-block-label">Adults <span class="req">*</span></div>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" id="adultMinus">&minus;</button>
                                <div>
                                    <div class="qty-val" id="adultDisplay">1</div>
                                    <div class="qty-sub">adults</div>
                                </div>
                                <button type="button" class="qty-btn" id="adultPlus">+</button>
                            </div>
                        </div>
                        <div class="qty-block">
                            <div class="qty-block-label">Children</div>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" id="childMinus">&minus;</button>
                                <div>
                                    <div class="qty-val" id="childDisplay">0</div>
                                    <div class="qty-sub">children</div>
                                </div>
                                <button type="button" class="qty-btn" id="childPlus">+</button>
                            </div>
                        </div>
                        <div class="qty-block">
                            <div class="qty-block-label">
                                Infants
                                <span class="infant-free-note">(Free)</span>
                            </div>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" id="infantMinus">&minus;</button>
                                <div>
                                    <div class="qty-val" id="infantDisplay">0</div>
                                    <div class="qty-sub">infants</div>
                                </div>
                                <button type="button" class="qty-btn" id="infantPlus">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="price-summary" id="priceCalculation">
                        Total: 1 &times; Rp 0 = <strong>Rp 0</strong>
                        <small>Price is calculated based on Adults and Children. Infants are free.</small>
                    </div>

                    <input type="hidden" name="adult_count" id="adultCount" value="1">
                    <input type="hidden" name="child_count" id="childCount" value="0">
                    <input type="hidden" name="infant_count" id="infantCount" value="0">
                </div>

                <!-- Step 3: Representative -->
                <div class="booking-step step-hidden" id="step3">
                    <div class="bk-section-title">Guest Information</div>

                    <div class="form-group">
                        <label>Guest Type  <span class="req">*</span></label>
                        <select class="form-control" name="visitor_type" id="visitorType" required>
                            @foreach($visitorTypes as $key => $label)
                                <option value="{{ $key }}" {{ $key === 'WI' ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Full Name <span class="req">*</span></label>
                        <input type="text" class="form-control" name="representative_name"
                            placeholder="Representative Name" value="{{ old('representative_name') }}" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Phone Number <span class="req">*</span></label>
                            <input type="text" class="form-control" name="representative_phone"
                                placeholder="08xx-xxxx-xxxx" value="{{ old('representative_phone') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Visit Date <span class="req">*</span></label>
                            <input type="date" class="form-control" name="visit_date"
                                min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Full Address <span class="req">*</span></label>
                        <textarea class="form-control" name="representative_address"
                            placeholder="Full Address/Location" required>{{ old('representative_address') }}</textarea>
                    </div>
                </div>

                <!-- Step 4: Schedule -->
                <div class="booking-step step-hidden" id="step4">
                    <div class="bk-section-title">Select Tour Schedule</div>
                    <div id="participantInfo" class="participant-info"></div>

                    <div class="session-cols">
                        @foreach($tours as $tour)
                           @php
                                $sessions = ($tourSessions[$tour->id] ?? collect())
                                    ->sortBy('start_time')
                                    ->values();
                            @endphp
                            <div class="session-col tour-session-group" data-tour-id="{{ $tour->id }}" data-tour-name="{{ $tour->name }}">
                                <div class="session-col-title">
                                    {{ $tour->name }}
                                    <span class="selected-badge selected-tour-info" data-tour="{{ $tour->id }}"
                                        ></span>
                                </div>
                                <div class="tour-sessions-container" data-tour-id="{{ $tour->id }}">
                                    @forelse($sessions as $session)
                                        <div class="session-item"
                                            data-session="{{ $session->id }}"
                                            data-tour-id="{{ $tour->id }}"
                                            data-capacity="{{ $session->capacity }}"
                                            data-booked="{{ $session->booked }}"
                                            data-start-time="{{ $session->start_time }}"
                                            data-end-time="{{ $session->end_time }}">
                                            <div class="session-item-top">
                                                <span class="session-item-name">{{ $session->label }}</span>
                                                <span class="session-item-guide">{{ $session->educator ? $session->educator->name : '-' }}</span>
                                            </div>
                                            <div class="session-progress">
                                                <div class="session-progress-bar"
                                                    style="width:{{ $session->booking_percentage }}%; background:{{ $session->bar_color }};">
                                                </div>
                                            </div>
                                            <div class="session-item-bottom">
                                                <span class="session-item-count">{{ $session->booked }}/{{ $session->capacity }}</span>
                                                <span class="session-badge"
                                                    style="background:{{ $session->status_background }}; color:{{ $session->status_color }};">{{ $session->status }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted small py-2 no-session-message">
                                            No available sessions for this tour.
                                        </div>
                                    @endforelse
                                </div>
                                <input type="hidden" name="tour_session_{{ $tour->id }}" class="tour-session-input"
                                    data-tour-id="{{ $tour->id }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation -->
                <div class="bk-nav">
                    <button type="button" class="btn-nav btn-back" id="prevBtn" onclick="changeStep(-1)" disabled>Back</button>
                    <button type="button" class="btn-nav btn-next" id="nextBtn" onclick="changeStep(1)" disabled>Next</button>
                    <button type="submit" class="btn-nav btn-submit" id="submitBtn">Confirm Booking</button>
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
        let infantCount = 0;
        const packages = @json($packages);
        const packageTourIds = @json($packageTourIds);
        const tours = @json($tours);

        let selectedSessions = {};

        function normalizeTourIds(ids) {
            if (Array.isArray(ids)) {
                return ids.map(id => Number(id)).filter(id => Number.isFinite(id));
            }
            if (ids && typeof ids === 'object') {
                return Object.values(ids).map(id => Number(id)).filter(id => Number.isFinite(id));
            }
            return [];
        }

        function getPackageTourIds() {
            if (!selectedPackage) return [];
            const key = String(selectedPackage);
            return normalizeTourIds(packageTourIds[key] ?? packageTourIds[selectedPackage] ?? []);
        }

        function allTourSessionsSelected() {
            const requiredTourIds = getPackageTourIds();
            for (const tourId of requiredTourIds) {
                const input = $(`.tour-session-input[data-tour-id="${tourId}"]`);
                if (!input.val()) return false;
            }
            return true;
        }

        function getMissingSelectedTourNames() {
            const requiredTourIds = getPackageTourIds();

            return requiredTourIds
                .filter(function (tourId) {
                    const input = $(`.tour-session-input[data-tour-id="${tourId}"]`);
                    return !input.val();
                })
                .map(function (tourId) {
                    const group = $(`.tour-session-group[data-tour-id="${tourId}"]`);
                    return group.data('tour-name') || `Tour ${tourId}`;
                });
        }

        function getToursWithoutVisibleSessions() {
            const requiredTourIds = getPackageTourIds();

            return requiredTourIds
                .filter(function (tourId) {
                    const visibleSessions = $(`.tour-session-group[data-tour-id="${tourId}"] .session-item:visible`).length;
                    return visibleSessions === 0;
                })
                .map(function (tourId) {
                    const group = $(`.tour-session-group[data-tour-id="${tourId}"]`);
                    return group.data('tour-name') || `Tour ${tourId}`;
                });
        }

        function updateVisibleTourGroups() {
            const requiredTourIds = getPackageTourIds();
            $('.tour-session-group').each(function () {
                const tourId = Number($(this).data('tour-id'));
                if (requiredTourIds.includes(tourId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                    $(this).find('.tour-session-input').val('');
                    $(this).find('.session-item').removeClass('selected');
                    $(this).find('.selected-tour-info').hide();
                    delete selectedSessions[tourId];
                }
            });
        }

        function parseToMinutes(isoString) {
            if (!isoString) return null;
            const date = new Date(isoString);
            return date.getUTCHours() * 60 + date.getUTCMinutes();
        }

        function updateSessionAvailability() {
            const requiredTourIds = getPackageTourIds();

            requiredTourIds.forEach(function (tourId) {
                const otherSelections = Object.entries(selectedSessions)
                    .filter(([tid]) => Number(tid) !== tourId)
                    .map(([, data]) => data);

                if (otherSelections.length === 0) {
                    $(`.tour-session-group[data-tour-id="${tourId}"] .session-item`).each(function () {
                        enableSession($(this));
                    });
                    return;
                }

                $(`.tour-session-group[data-tour-id="${tourId}"] .session-item`).each(function () {
                    const $item = $(this);
                    const itemStart = parseToMinutes($item.data('start-time'));
                    const itemEnd   = parseToMinutes($item.data('end-time'));

                    if (itemStart === null || itemEnd === null) return;

                    let isBlocked = false;

                    otherSelections.forEach(function (other) {
                        const otherStart = parseToMinutes(other.startTime);
                        const otherEnd   = parseToMinutes(other.endTime);

                        if (otherStart === null || otherEnd === null) return;

                        const gapAfter  = itemStart - otherEnd;
                        const gapBefore = otherStart - itemEnd;

                        const validAfter  = gapAfter >= 60;
                        const validBefore = gapBefore >= 60;

                        if (!validAfter && !validBefore) {
                            isBlocked = true;
                        }
                    });

                    if (isBlocked) {
                        disableSession($item);
                    } else {
                        enableSession($item);
                    }
                });
            });
        }

        function disableSession($item) {
            $item.addClass('session-disabled');
            $item.css({
                'opacity': '0.4',
                'cursor': 'not-allowed',
                'background': '#F0F0F0',
                'border-color': '#CCCCCC',
            });
            $item.attr('data-disabled', '1');
        }

        function enableSession($item) {
            if ($item.hasClass('selected')) return;
            $item.removeClass('session-disabled');
            $item.css({
                'opacity': '',
                'cursor': 'pointer',
                'background': '',
                'border-color': '',
            });
            $item.attr('data-disabled', '0');
        }

        $(document).on('click', '.pkg-card', function () {
            $('.pkg-card').removeClass('selected');
            $(this).addClass('selected');
            selectedPackage = $(this).data('package');
            $('#selectedPackage').val(selectedPackage);
            selectedSessions = {};
            updateVisibleTourGroups();
            $('#nextBtn').prop('disabled', false);
        });

        $(document).on('click', '.session-item', function () {
            if ($(this).attr('data-disabled') === '1') return;

            const tourId    = $(this).data('tour-id');
            const sessionId = $(this).data('session');
            const capacity  = $(this).data('capacity');
            const booked    = $(this).data('booked');
            const available = capacity - booked;
            const startTime = $(this).data('start-time');
            const endTime   = $(this).data('end-time');
            const totalParticipants = adultCount + childCount + infantCount;

            if (available < totalParticipants) {
                alert('Capacity for this session is insufficient for the number of participants. Please choose another session or reduce the number of participants.');
                return;
            }

            if (selectedSessions[tourId]) {
                const prevSessionId = selectedSessions[tourId].sessionId;
                $(`.session-item[data-session="${prevSessionId}"]`).removeClass('selected');
            }

            $(`.session-item[data-tour-id="${tourId}"]`).removeClass('selected');
            $(this).addClass('selected');
            $(`.tour-session-input[data-tour-id="${tourId}"]`).val(sessionId);
            $(`.selected-tour-info[data-tour="${tourId}"]`).text($(this).find('.session-item-name').text()).show();

            selectedSessions[tourId] = { sessionId, startTime, endTime };

            updateSessionAvailability();

            if (allTourSessionsSelected()) {
                $('#submitBtn').prop('disabled', false);
            }
        });

        $('#adultPlus').click(function ()  { if (adultCount < 20)  { adultCount++;  updateQuantityDisplay(); } });
        $('#adultMinus').click(function () { if (adultCount > 1)   { adultCount--;  updateQuantityDisplay(); } });
        $('#childPlus').click(function ()  { if (childCount < 20)  { childCount++;  updateQuantityDisplay(); } });
        $('#childMinus').click(function () { if (childCount > 0)   { childCount--;  updateQuantityDisplay(); } });
        $('#infantPlus').click(function () { if (infantCount < 10) { infantCount++; updateQuantityDisplay(); } });
        $('#infantMinus').click(function () { if (infantCount > 0) { infantCount--; updateQuantityDisplay(); } });

        function updateQuantityDisplay() {
            $('#adultDisplay').text(adultCount);
            $('#childDisplay').text(childCount);
            $('#infantDisplay').text(infantCount);
            $('#adultCount').val(adultCount);
            $('#childCount').val(childCount);
            $('#infantCount').val(infantCount);

            if (selectedPackage) {
                const pkg = packages.find(p => p.id == selectedPackage);
                const paidCount = adultCount + childCount;
                const total = pkg.price * paidCount;
                $('#priceCalculation').html(
                    `Total: ${paidCount} &times; Rp ${pkg.price.toLocaleString('id-ID')} = <strong>Rp ${total.toLocaleString('id-ID')}</strong>` +
                    `<small>Price is calculated based on Adults and Children. Infants are free.</small>`
                );
            }
        }

        function updateStepTabs() {
            for (let i = 1; i <= 4; i++) {
                const tab = $(`#stepTab${i}`);
                tab.removeClass('active done');
                const numEl = tab.find('.bk-step-num');
                if (i < currentStep) {
                    tab.addClass('done');
                    numEl.text('✓');
                } else if (i === currentStep) {
                    tab.addClass('active');
                    numEl.text(i);
                } else {
                    numEl.text(i);
                }
            }
        }

        function changeStep(direction) {
            if (direction === 1) {
                if (currentStep === 1 && !selectedPackage) {
                    alert('Please select a package first');
                    return;
                }
                if (currentStep === 3) {
                    const name      = $('[name="representative_name"]').val();
                    const address   = $('[name="representative_address"]').val();
                    const phone     = $('[name="representative_phone"]').val();
                    const visitDate = $('[name="visit_date"]').val();
                    const visType   = $('[name="visitor_type"]').val();
                    if (!name || !address || !phone || !visitDate || !visType) {
                        alert('Please complete all Guest information');
                        return;
                    }
                }
            }

            currentStep += direction;
            $('.booking-step').hide();
            $(`#step${currentStep}`).show();
            updateStepTabs();

            if (currentStep === 2 && direction === 1) {
                const pkg = packages.find(p => p.id == selectedPackage);
                $('#selectedPackageInfo').html(`<div class="ctx-pill"><div class="ctx-pill-dot"></div>${pkg.label} — ${pkg.name}</div>`);
                updateQuantityDisplay();
                $('#nextBtn').prop('disabled', false);
            }

            if (currentStep === 4 && direction === 1) {
                const pkg = packages.find(p => p.id == selectedPackage);
                const totalCount = adultCount + childCount + infantCount;
                $('#participantInfo').html(
                    `<div class="ctx-pill"><div class="ctx-pill-dot"></div>` +
                    `${totalCount} guests (${adultCount} adults, ${childCount} children, ${infantCount} infants) &nbsp;•&nbsp; ${pkg ? pkg.name : ''}</div>`
                );
                updateVisibleTourGroups();

                selectedSessions = {};
                $('.tour-session-input').val('');
                $('.session-item').removeClass('selected');
                $('.selected-tour-info').hide();
                $('.session-item').each(function () { enableSession($(this)); });

                $('#nextBtn').prop('disabled', true);

                hideExpiredSessions();

                const toursWithoutSessions = getToursWithoutVisibleSessions();
                if (toursWithoutSessions.length > 0) {
                    alert('No sessions are available for: ' + toursWithoutSessions.join(', ') + '. Please choose another package or tour/date.');
                }
            }

            $('#prevBtn').prop('disabled', currentStep === 1);

            if (currentStep === 4) {
                $('#nextBtn').hide();
                $('#submitBtn').show().prop('disabled', false);
            } else {
                $('#nextBtn').show();
                $('#submitBtn').hide();
                if (currentStep === 1) {
                    $('#nextBtn').prop('disabled', !selectedPackage);
                } else if (currentStep === 2 || currentStep === 3) {
                    $('#nextBtn').prop('disabled', false);
                }
            }
        }

        function hideExpiredSessions() {
        const now = new Date();

        $('.session-item').each(function () {
            const startTimeStr = $(this).data('start-time');
            if (!startTimeStr) return;

            const startTime = new Date(startTimeStr);
            const graceTime = new Date(startTime.getTime() + 10 * 60* 1000);

            if (graceTime < now) {
                $(this).hide();
            } else {
                $(this).show();
            }
            });
        }

        $('#bookingForm').submit(function (e) {
            const missingTours = getMissingSelectedTourNames();
            if (missingTours.length > 0) {
                e.preventDefault();
                alert('Please choose a session first for: ' + missingTours.join(', '));
            }
        });

        $(document).ready(function () {
            setInterval(function () {
                if (currentStep === 4) {
                    hideExpiredSessions();
                }
            }, 60000);
        });
    </script>
@endpush
