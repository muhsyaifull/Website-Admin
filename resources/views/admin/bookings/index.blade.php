@extends('layouts.app')

@section('title', 'All Bookings - Admin')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            All Reservations
        </h1>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0">
                Reservation List - {{ $selectedDate->translatedFormat('d M Y') }}
            </h6>
            <form method="GET" action="{{ route('panel.bookings.index') }}"
                class="d-flex flex-column flex-md-row align-items-md-center">
                <div class="d-flex align-items-center mb-2 mb-md-0 mr-md-3">
                    <label for="date" class="mb-0 mr-2 text-muted">Date</label>
                    <input type="date" id="date" name="date" class="form-control form-control-sm"
                        value="{{ request('date', $selectedDate->format('Y-m-d')) }}" onchange="this.form.submit()">
                </div>
                <div class="d-flex align-items-center">
                    <label for="package_id" class="mb-0 mr-2 text-muted">Package</label>
                    <select id="package_id" name="package_id" class=" form-control form-control-sm" style="width: 200px"
                        onchange="this.form.submit()">
                        <option value="">All Packages</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->label }} - {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="card-body" style="overflow-x: auto; font-size: 15px;">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>Reservation Code</th>
                            <th>Package</th>
                            <th>Guest</th>
                            <th>Participants</th>
                            <th>Visit Date</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr class="text-center">
                                <td>
                                    <a href=" {{ route('panel.bookings.show', $booking) }}"
                                        class="font-weight-bold text-primary">
                                        {{ $booking->booking_code }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-primary" style="color: white;">
                                        {{ $booking->package->label }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $booking->package->name }}</small>
                                </td>
                                <td>
                                    <strong>{{ $booking->representative_name }}</strong><br>
                                    <small class="text-muted">{{ $booking->representative_phone }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $booking->total_participants }} participants
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $booking->adult_count }} adults, {{ $booking->child_count }} children
                                    </small>
                                </td>
                                <td>{{ $booking->formatted_visit_date }}</td>
                                <td class="font-weight-bold">{{ $booking->formatted_total_price }}</td>
                                <td>
                                    <span class="badge" style="background: {{ $booking->status_color }}; color: white;">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                                <td>{{ $booking->user->name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('panel.bookings.show', $booking) }}" class="btn btn-info btn-sm"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No reservations on {{ $selectedDate->translatedFormat('d M Y') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($bookings, 'links'))
                <div class="d-flex justify-content-center">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0">
                Monthly Reservation Summary per Package
            </h6>
            <small class="text-muted">
                {{ $monthStart->translatedFormat('d M Y') }} - {{ $monthEnd->translatedFormat('d M Y') }}
            </small>
        </div>
        <div class="card-body" style="overflow-x: auto; font-size: 15px;">
            @if($monthlyPackageSummary->isEmpty())
                <div class="text-center text-muted py-3">
                    No reservation data found in this month.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 60px;">No</th>
                                <th>Package</th>
                                <th style="width: 240px;">Total Reservations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyPackageSummary as $index => $summary)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge badge-primary" style="color: white;">
                                            {{ $summary['label'] }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $summary['name'] }}</small>
                                    </td>
                                    <td class="text-center font-weight-bold">{{ number_format($summary['total_reservations']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Grand Total</th>
                                <th class="text-center text-success">{{ number_format($monthlyReservationTotal) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // DataTable initialization if needed
        $(document).ready(function () {
            $('#dataTable').DataTable({
                "ordering": true,
                "searching": true,
                "paging": true,
                "lengthChange": true,
                "pageLength": 25,
                "responsive": true,
                "order": [[0, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [8] }
                ]
            });
        });
    </script>
@endpush