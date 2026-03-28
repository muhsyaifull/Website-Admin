@extends('layouts.app')

@section('title', 'Booking Confirmation')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #printTicket, #printTicket * { visibility: visible; }
        #printTicket { position: fixed; top: 0; left: 0; width: 100%; }
    }
</style>
@endpush

@section('content')
    <div class="card shadow" style="max-width: 600px; margin: 0 auto;">
        <!-- Header -->
        <div class="card-header text-white text-center" style="background: #3A1A0E; padding: 24px;">
            <div style="font-size: 13px; font-weight: 700; color: #1A7A40; letter-spacing: .5px; margin-bottom: 4px;">
                RESERVATION CONFIRMED
            </div>
            <div style="font-size: 12px; color: #B08878;">
                Transaction has been recorded successfully
            </div>
        </div>

        <div class="card-body text-center">

            <!-- PRINTABLE TICKET -->
            <div id="printTicket" class="mx-auto mb-4" style="max-width: 420px;">
                <div style="background: #FFFCFA; border: 1px solid #E0CEC6; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(74,34,24,0.08); font-family: 'DM Sans', sans-serif;">

                    <!-- Ticket Header -->
                    <div style="background: #3A1A0E; padding: 20px 24px 16px;">
                        <div style="font-size: 10px; letter-spacing: 2px; color: #B08878; text-transform: uppercase; margin-bottom: 4px;">
                            Rumah Atsiri Indonesia
                        </div>
                        <div style="font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 2px;">
                            Tour Schedule Confirmation
                        </div>
                        <div style="font-size: 11px; color: #B08878;">
                            {{ $booking->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>

                    <!-- Booking Code Banner -->
                    <div style="background: #F7F0EC; padding: 10px 24px; border-bottom: 1px solid #E0CEC6; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: #9E8078;">
                            Reservation Code
                        </div>
                        <div style="font-size: 15px; font-weight: 700; color: #3A1A0E; font-family: 'DM Mono', monospace; letter-spacing: 1px;">
                            {{ $booking->booking_code }}
                        </div>
                    </div>

                    <!-- Ticket Body -->
                    <div style="padding: 20px 24px;">

                        <!-- Package & Visitor Type -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E4DC;">
                            <div>
                                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9E8078; margin-bottom: 3px;">Package</div>
                                <div style="font-size: 14px; font-weight: 600; color: #3A1A0E;">{{ $booking->package->name }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9E8078; margin-bottom: 3px;">Guest Type</div>
                                <div style="display: inline-block; font-size: 11px; font-weight: 600; background: #EAF0FF; color: #3B5BDB; border-radius: 4px; padding: 2px 10px;">
                                    {{ $booking->visitor_type_label }}
                                </div>
                            </div>
                        </div>

                        <!-- Participants -->
                        <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E4DC;">
                            <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9E8078; margin-bottom: 8px;">Participants</div>
                            <div style="display: flex; gap: 10px;">
                                <div style="flex: 1; background: #F7F0EC; border-radius: 8px; padding: 10px; text-align: center;">
                                    <div style="font-size: 20px; font-weight: 700; color: #3A1A0E; font-family: 'DM Mono', monospace;">{{ $booking->adult_count }}</div>
                                    <div style="font-size: 10px; color: #9E8078; text-transform: uppercase; letter-spacing: .5px;">Adults</div>
                                </div>
                                <div style="flex: 1; background: #F7F0EC; border-radius: 8px; padding: 10px; text-align: center;">
                                    <div style="font-size: 20px; font-weight: 700; color: #3A1A0E; font-family: 'DM Mono', monospace;">{{ $booking->child_count }}</div>
                                    <div style="font-size: 10px; color: #9E8078; text-transform: uppercase; letter-spacing: .5px;">Children</div>
                                </div>
                                @if($booking->infant_count > 0)
                                    <div style="flex: 1; background: #F7F0EC; border-radius: 8px; padding: 10px; text-align: center;">
                                        <div style="font-size: 20px; font-weight: 700; color: #3A1A0E; font-family: 'DM Mono', monospace;">{{ $booking->infant_count }}</div>
                                        <div style="font-size: 10px; color: #9E8078; text-transform: uppercase; letter-spacing: .5px;">Infants</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tour Sessions -->
                        <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E4DC;">
                            <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9E8078; margin-bottom: 8px;">Tour Schedule</div>
                            @if(count($tourSessionMap) > 0)
                                @foreach($tourSessionMap as $tourId => $session)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #F7F0EC; border-radius: 7px; margin-bottom: 6px;">
                                        <div style="font-size: 12px; color: #7B3F2A; font-weight: 600;">{{ $session->tour->name }}</div>
                                        <div style="font-size: 12px; color: #3A1A0E; font-weight: 500;">{{ $session->label }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div style="font-size: 12px; color: #9E8078;">No sessions assigned</div>
                            @endif
                        </div>

                        <!-- Visit Date -->
                        <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E4DC; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9E8078;">Visit Date</div>
                            <div style="font-size: 14px; font-weight: 600; color: #3A1A0E;">{{ $booking->formatted_visit_date }}</div>
                        </div>

                        <!-- Total Payment -->
                        <div style="background: #F0FFF4; border: 1px solid #C3E6CB; border-radius: 8px; padding: 14px 16px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #5A8A6A;">Total Payment</div>
                                @if($booking->infant_count > 0)
                                    <div style="font-size: 10px; color: #9E8078; margin-top: 2px;">
                                        {{ $booking->adult_count + $booking->child_count }} pax &times; {{ $booking->formatted_unit_price }} &mdash; infants free
                                    </div>
                                @else
                                    <div style="font-size: 10px; color: #9E8078; margin-top: 2px;">
                                        {{ $booking->adult_count + $booking->child_count }} pax &times; {{ $booking->formatted_unit_price }}
                                    </div>
                                @endif
                            </div>
                            <div style="font-size: 18px; font-weight: 700; color: #1A7A40;">
                                {{ $booking->formatted_total_price }}
                            </div>
                        </div>

                    </div>

                    <!-- Ticket Footer -->
                    <div style="background: #F7F0EC; padding: 12px 24px; border-top: 1px solid #E0CEC6; text-align: center;">
                        <div style="font-size: 11px; color: #9E8078; line-height: 1.6;">
                            Please arrive <strong style="color: #7B3F2A;">10 minutes early</strong> at the main gathering point.
                        </div>
                    </div>

                </div>
            </div>
            {{-- END PRINTABLE TICKET --}}

            <!-- Transaction Detail -->
            <div class="mx-auto mb-4" style="max-width: 420px; background: #F8F9FA; border: 1px solid #E8D8CF; border-radius: 10px; padding: 18px 24px; text-align: left;">
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9E8078; margin-bottom: 12px;">
                    Transaction Detail
                </div>
                <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                    <tr>
                        <td style="color: #9E8078; padding: 4px 0; width: 40%;">Guest</td>
                        <td style="color: #3A1A0E; font-weight: 500; padding: 4px 0;">{{ $booking->representative_name }}</td>
                    </tr>
                    <tr>
                        <td style="color: #9E8078; padding: 4px 0;">Phone</td>
                        <td style="color: #3A1A0E; font-weight: 500; padding: 4px 0;">{{ $booking->representative_phone }}</td>
                    </tr>
                    <tr>
                        <td style="color: #9E8078; padding: 4px 0; vertical-align: top;">Address</td>
                        <td style="color: #3A1A0E; font-weight: 500; padding: 4px 0;">{{ $booking->representative_address }}</td>
                    </tr>
                    <tr>
                        <td style="color: #9E8078; padding: 4px 0;">Created by</td>
                        <td style="color: #3A1A0E; font-weight: 500; padding: 4px 0;">
                            {{ $booking->user->name }} &bull; {{ $booking->created_at->format('H:i, d M Y') }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Action Buttons — tidak ikut tercetak -->
            <div class="mt-3">
                <button onclick="window.print()"
                    style="display: inline-block; padding: 9px 20px; border-radius: 7px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; border: 1.5px solid #3B5BDB; background: #3B5BDB; color: #fff; margin: 4px; text-decoration: none;">
                    Print Ticket
                </button>
                <a href="{{ route('kasir.booking.create') }}"
                    style="display: inline-block; padding: 9px 20px; border-radius: 7px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; border: 1.5px solid #1A7A40; background: #1A7A40; color: #fff; margin: 4px; text-decoration: none;">
                    New Transaction
                </a>
                <a href="{{ route('kasir.index') }}"
                    style="display: inline-block; padding: 9px 20px; border-radius: 7px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; border: 1.5px solid #D0C0B8; background: #fff; color: #7A5A50; margin: 4px; text-decoration: none;">
                    Dashboard
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
        setTimeout(function () {
            if (confirm('Create new booking?')) {
                window.location.href = "{{ route('kasir.booking.create') }}";
            }
        }, 30000);
    </script>
@endpush