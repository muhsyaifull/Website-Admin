@extends('layouts.app')

@section('title', 'Create Tour Booking')

@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        .booking-wrap * {
            box-sizing: border-box;
        }

        .booking-wrap {
            font-family: 'DM Sans', sans-serif;
            width: 100%;
        }

        /* ── Header ── */
        .bk-header {
            background: #3A1A0E;
            color: #fff;
            padding: 16px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;

        }

        .bk-header-brand {
            font-size: 11px;
            color: #B08878;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .bk-header-title {
            font-size: 16px;
            font-weight: 700;
            color: #fff;
        }

        .bk-header-meta {
            text-align: right;
            font-size: 12px;
            color: #B08878;
            line-height: 1.6;
        }

        /* ── Steps ── */
        .bk-steps {
            background: #F7F0EC;
            padding: 0;
            border-bottom: 1px solid #E8D8CF;
            display: flex;
        }

        .bk-step {
            flex: 1;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-right: 1px solid #E8D8CF;
            position: relative;
            transition: background .2s;
        }

        .bk-step:last-child {
            border-right: none;
        }

        .bk-step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #D5C2BA;
            color: #9E8078;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .2s;
            font-family: 'DM Mono', monospace;
        }

        .bk-step-label {
            font-size: 12px;
            color: #9E8078;
            font-weight: 500;
            white-space: nowrap;
        }

        .bk-step.active .bk-step-num {
            background: #7B3F2A;
            color: #fff;
        }

        .bk-step.active .bk-step-label {
            color: #3A1A0E;
            font-weight: 600;
        }

        .bk-step.done .bk-step-num {
            background: #7B3F2A;
            color: #fff;
        }

        .bk-step.done .bk-step-label {
            color: #7B3F2A;
        }

        /* ── Body ── */
        .bk-body {
            background: #fff;
            padding: 28px;
            border: 1px solid #E8D8CF;
            border-top: none;

        }

        .bk-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #7B3F2A;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #F0E4DC;
        }

        /* ── Package Cards ── */
        .pkg-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pkg-card {
            border: 1.5px solid #E0CEC6;
            border-radius: 8px;
            padding: 14px 18px;
            cursor: pointer;
            background: #FDFAF8;
            transition: all .15s ease;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .pkg-card:hover {
            border-color: #A0604A;
            background: #FDF6F3;
        }

        .pkg-card.selected {
            border-color: #7B3F2A;
            border-width: 2px;
            background: #FDF0EA;
        }

        .pkg-card-radio {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #C0A898;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }

        .pkg-card.selected .pkg-card-radio {
            border-color: #7B3F2A;
            background: #7B3F2A;
        }

        .pkg-card.selected .pkg-card-radio::after {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fff;
        }

        .pkg-card-info {
            flex: 1;
        }

        .pkg-card-name {
            font-size: 14px;
            font-weight: 600;
            color: #3A1A0E;
            margin-bottom: 4px;
        }

        .pkg-card-label {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            background: #EAF0FF;
            color: #3B5BDB;
            border-radius: 4px;
            padding: 1px 7px;
            margin-right: 6px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .pkg-card-includes {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 6px;
        }

        .pkg-tag {
            font-size: 11px;
            color: #5A6A8A;
            background: #F0F4FF;
            border: 1px solid #D0DAFF;
            border-radius: 4px;
            padding: 1px 8px;
        }

        .pkg-card-price {
            font-size: 15px;
            font-weight: 700;
            color: #3B5BDB;
            white-space: nowrap;
        }

        /* ── Qty Controls ── */
        .qty-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        .qty-block {
            flex: 1;
            border: 1.5px solid #E0CEC6;
            border-radius: 8px;
            padding: 14px 16px;
            background: #FDFAF8;
        }

        .qty-block-label {
            font-size: 11px;
            font-weight: 700;
            color: #9E8078;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 12px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1.5px solid #C0A898;
            background: #fff;
            color: #7B3F2A;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            transition: all .15s;
            font-family: 'DM Mono', monospace;
            padding: 0;
        }

        .qty-btn:hover {
            background: #F5EDE8;
            border-color: #7B3F2A;
        }

        .qty-btn:active {
            transform: scale(.94);
        }

        .qty-val {
            font-size: 28px;
            font-weight: 700;
            color: #3A1A0E;
            font-family: 'DM Mono', monospace;
            min-width: 40px;
            text-align: center;
        }

        .qty-sub {
            font-size: 11px;
            color: #B09888;
            text-align: center;
            margin-top: 2px;
        }

        .price-summary {
            background: #F7F0EC;
            border: 1.5px solid #E0CEC6;
            border-radius: 8px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #5A2A18;
            text-align: center;
        }

        /* ── Form ── */
        .form-row-2 {
            display: flex;
            gap: 16px;
        }

        .form-row-2 .form-group {
            flex: 1;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 700;
            color: #6A4030;
            text-transform: uppercase;
            letter-spacing: .6px;
            display: block;
            margin-bottom: 5px;
        }

        .form-group label .req {
            color: #E03030;
        }

        .form-control {
            width: 100%;
            border: 1.5px solid #E0CEC6;
            border-radius: 7px;
            padding: 9px 12px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: #3A1A0E;
            background: #FDFAF8;
            transition: border-color .15s;
            outline: none;
        }

        .form-control:focus {
            border-color: #7B3F2A;
            background: #fff;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 72px;
        }

        /* ── Sessions ── */
        .session-cols {
            display: flex;
            gap: 20px;
        }

        .session-col {
            flex: 1;
        }

        .session-col-title {
            font-size: 12px;
            font-weight: 700;
            color: #3B5BDB;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 10px;
        }

        .session-item {
            border: 1.5px solid #E0CEC6;
            border-radius: 7px;
            padding: 10px 12px;
            cursor: pointer;
            background: #FDFAF8;
            margin-bottom: 7px;
            transition: all .15s;
        }

        .session-item:hover {
            border-color: #A0604A;
            background: #FDF6F3;
        }

        .session-item.selected {
            border-color: #7B3F2A;
            border-width: 2px;
            background: #FDF0EA;
        }

        .session-item-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .session-item-name {
            font-size: 13px;
            font-weight: 600;
            color: #3A1A0E;
        }

        .session-item-guide {
            font-size: 11px;
            color: #9E8078;
        }

        .session-progress {
            height: 4px;
            border-radius: 2px;
            background: #EDD8CC;
            margin-bottom: 5px;
            overflow: hidden;
        }

        .session-progress-bar {
            height: 100%;
            border-radius: 2px;
        }

        .session-item-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .session-item-count {
            font-size: 11px;
            color: #9E8078;
            font-family: 'DM Mono', monospace;
        }

        .session-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 1px 8px;
            border-radius: 4px;
            letter-spacing: .4px;
        }

        .selected-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            background: #7B3F2A;
            color: #fff;
            border-radius: 4px;
            padding: 1px 8px;
            margin-left: 6px;
            letter-spacing: .4px;
            vertical-align: middle;
        }

        /* ── Navigation ── */
        .bk-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1.5px solid #F0E4DC;
        }

        .btn-nav {
            padding: 9px 22px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            border: 1.5px solid transparent;
            transition: all .15s;
            letter-spacing: .3px;
        }

        .btn-back {
            background: #fff;
            border-color: #D0C0B8;
            color: #7A5A50;
        }

        .btn-back:hover {
            background: #F5EDE8;
            border-color: #B09888;
        }

        .btn-back:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .btn-next {
            background: #7B3F2A;
            border-color: #7B3F2A;
            color: #fff;
        }

        .btn-next:hover {
            background: #6A3020;
        }

        .btn-next:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .btn-submit {
            background: #1A7A40;
            border-color: #1A7A40;
            color: #fff;
        }

        .btn-submit:hover {
            background: #156030;
        }

        /* ── Pkg info pill (step 2+) ── */
        .ctx-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #F7F0EC;
            border: 1px solid #E0CEC6;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            color: #7B3F2A;
            font-weight: 500;
            margin-bottom: 18px;
        }

        .ctx-pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #7B3F2A;
        }
    </style>

    <div class="booking-wrap">
        <div class="bk-header">
            <div>
                <div class="bk-header-brand">Rumah Atsiri Indonesia</div>
                <div class="bk-header-title">Cashier — Tour Booking</div>
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
                <div class="bk-step-label">Representative</div>
            </div>
            <div class="bk-step" id="stepTab4">
                <div class="bk-step-num">4</div>
                <div class="bk-step-label">Schedule</div>
            </div>
        </div>

        <div class="bk-body">
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
                                    <div class="pkg-card-includes">
                                        @foreach($package->includes as $item)
                                            <span class="pkg-tag">{{ $item }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="pkg-card-price">{{ $package->formatted_price }}</div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="package_id" id="selectedPackage">
                </div>

                <!-- Step 2: Participants -->
                <div class="booking-step" id="step2" style="display:none;">
                    <div class="bk-section-title">Participants</div>
                    <div id="selectedPackageInfo"></div>

                    <div class="qty-row">
                        <div class="qty-block">
                            <div class="qty-block-label">Adults <span style="color:#E03030">*</span></div>
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
                            <div class="qty-block-label">Children <span
                                    style="color:#28A745; font-size:10px; text-transform:none; font-weight:500;">(Free)</span>
                            </div>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" id="childMinus">&minus;</button>
                                <div>
                                    <div class="qty-val" id="childDisplay">0</div>
                                    <div class="qty-sub">children</div>
                                </div>
                                <button type="button" class="qty-btn" id="childPlus">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="price-summary" id="priceCalculation">
                        Total: 1 &times; Rp 75.000 = Rp 75.000
                    </div>

                    <input type="hidden" name="adult_count" id="adultCount" value="1">
                    <input type="hidden" name="child_count" id="childCount" value="0">
                </div>

                <!-- Step 3: Representative -->
                <div class="booking-step" id="step3" style="display:none;">
                    <div class="bk-section-title">Representative Information</div>

                    <div class="form-group">
                        <label>Full Name <span class="req">*</span></label>
                        <input type="text" class="form-control" name="representative_name"
                            placeholder="Visitor representative name" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Phone Number <span class="req">*</span></label>
                            <input type="text" class="form-control" name="representative_phone" placeholder="08xx-xxxx-xxxx"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Visit Date <span class="req">*</span></label>
                            <input type="date" class="form-control" name="visit_date" min="{{ date('Y-m-d') }}"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Full Address <span class="req">*</span></label>
                        <textarea class="form-control" name="representative_address" placeholder="Street, City, Province"
                            required></textarea>
                    </div>
                </div>

                <!-- Step 4: Schedule -->
                <div class="booking-step" id="step4" style="display:none;">
                    <div class="bk-section-title">Select Tour Schedule</div>
                    <div id="participantInfo" style="margin-bottom:14px;"></div>

                    <div class="session-cols">
                        @foreach($tours as $tour)
                            @php $sessions = $tourSessions[$tour->id] ?? collect(); @endphp
                            <div class="session-col tour-session-group" data-tour-id="{{ $tour->id }}">
                                <div class="session-col-title">
                                    {{ $tour->name }}
                                    <span class="selected-badge selected-tour-info" data-tour="{{ $tour->id }}"
                                        style="display:none;"></span>
                                </div>
                                <div class="tour-sessions-container" data-tour-id="{{ $tour->id }}">
                                    @foreach($sessions as $session)
                                        <div class="session-item" data-session="{{ $session->id }}" data-tour-id="{{ $tour->id }}"
                                            data-capacity="{{ $session->capacity }}" data-booked="{{ $session->booked }}">
                                            <div class="session-item-top">
                                                <span class="session-item-name">{{ $session->label }}</span>
                                                <span
                                                    class="session-item-guide">{{ $session->educator ? $session->educator->name : '-' }}</span>
                                            </div>
                                            <div class="session-progress">
                                                <div class="session-progress-bar"
                                                    style="width:{{ $session->booking_percentage }}%; background:{{ $session->bar_color }};">
                                                </div>
                                            </div>
                                            <div class="session-item-bottom">
                                                <span
                                                    class="session-item-count">{{ $session->booked }}/{{ $session->capacity }}</span>
                                                <span class="session-badge"
                                                    style="background:{{ $session->status_background }}; color:{{ $session->status_color }};">{{ $session->status }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="tour_session_{{ $tour->id }}" class="tour-session-input"
                                    data-tour-id="{{ $tour->id }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation -->
                <div class="bk-nav">
                    <button type="button" class="btn-nav btn-back" id="prevBtn" onclick="changeStep(-1)"
                        disabled>Back</button>
                    <button type="button" class="btn-nav btn-next" id="nextBtn" onclick="changeStep(1)"
                        disabled>Next</button>
                    <button type="submit" class="btn-nav btn-submit" id="submitBtn" style="display:none;">Confirm
                        Booking</button>
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
        const packages = @json($packages);
        const tours = @json($tours);

        function getPackageTourIds() {
            if (!selectedPackage) return [];
            const pkg = packages.find(p => p.id == selectedPackage);
            return pkg && pkg.tours ? pkg.tours.map(t => t.id) : tours.map(t => t.id);
        }

        function allTourSessionsSelected() {
            const requiredTourIds = getPackageTourIds();
            for (const tourId of requiredTourIds) {
                const input = $(`.tour-session-input[data-tour-id="${tourId}"]`);
                if (!input.val()) return false;
            }
            return true;
        }

        function updateVisibleTourGroups() {
            const requiredTourIds = getPackageTourIds();
            $('.tour-session-group').each(function () {
                const tourId = $(this).data('tour-id');
                if (requiredTourIds.includes(tourId)) {
                    $(this).show();
                } else {
                    $(this).hide();
                    $(this).find('.tour-session-input').val('');
                    $(this).find('.session-item').removeClass('selected');
                    $(this).find('.selected-tour-info').hide();
                }
            });
        }

        $(document).on('click', '.pkg-card', function () {
            $('.pkg-card').removeClass('selected');
            $(this).addClass('selected');
            selectedPackage = $(this).data('package');
            $('#selectedPackage').val(selectedPackage);
            $('#nextBtn').prop('disabled', false);
        });

        $(document).on('click', '.session-item', function () {
            const tourId = $(this).data('tour-id');
            const sessionId = $(this).data('session');
            const capacity = $(this).data('capacity');
            const booked = $(this).data('booked');
            const available = capacity - booked;
            const totalParticipants = adultCount + childCount;

            if (available < totalParticipants) {
                alert('Session capacity is insufficient for the selected number of participants!');
                return;
            }

            $(`.session-item[data-tour-id="${tourId}"]`).removeClass('selected');
            $(this).addClass('selected');
            $(`.tour-session-input[data-tour-id="${tourId}"]`).val(sessionId);
            $(`.selected-tour-info[data-tour="${tourId}"]`).text($(this).find('.session-item-name').text()).show();

            if (allTourSessionsSelected()) {
                $('#submitBtn').prop('disabled', false);
            }
        });

        $('#adultPlus').click(function () { if (adultCount < 20) { adultCount++; updateQuantityDisplay(); } });
        $('#adultMinus').click(function () { if (adultCount > 1) { adultCount--; updateQuantityDisplay(); } });
        $('#childPlus').click(function () { if (childCount < 10) { childCount++; updateQuantityDisplay(); } });
        $('#childMinus').click(function () { if (childCount > 0) { childCount--; updateQuantityDisplay(); } });

        function updateQuantityDisplay() {
            $('#adultDisplay').text(adultCount);
            $('#childDisplay').text(childCount);
            $('#adultCount').val(adultCount);
            $('#childCount').val(childCount);
            if (selectedPackage) {
                const pkg = packages.find(p => p.id == selectedPackage);
                const total = pkg.price * adultCount;
                $('#priceCalculation').html(`Total: ${adultCount} &times; Rp ${pkg.price.toLocaleString('id-ID')} = <strong>Rp ${total.toLocaleString('id-ID')}</strong>`);
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
                    alert('Please select a bundling package first');
                    return;
                }
                if (currentStep === 3) {
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
                $('#participantInfo').html(`<div class="ctx-pill"><div class="ctx-pill-dot"></div>${adultCount + childCount} participants &nbsp;•&nbsp; ${pkg ? pkg.name : ''}</div>`);
                updateVisibleTourGroups();
                $('#nextBtn').prop('disabled', true);
            }

            $('#prevBtn').prop('disabled', currentStep === 1);

            if (currentStep === 4) {
                $('#nextBtn').hide();
                $('#submitBtn').show().prop('disabled', !allTourSessionsSelected());
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

        $('#bookingForm').submit(function (e) {
            if (!allTourSessionsSelected()) {
                e.preventDefault();
                alert('Please select schedules for all required tours');
            }
        });
    </script>
@endpush