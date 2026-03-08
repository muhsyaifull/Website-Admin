@extends('layouts.app')

@section('title', 'All Bookings - Admin')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list text-primary"></i> All Bookings
        </h1>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Booking List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>Package</th>
                            <th>Representative</th>
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
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                        class="font-weight-bold text-primary">
                                        {{ $booking->booking_code }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge" style="background: {{ $booking->package->color }}; color: white;">
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
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-info btn-sm"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No bookings yet
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
                "order": [[0, "desc"]], // Sort by booking code desc
                "columnDefs": [
                    { "orderable": false, "targets": [8] } // Disable sorting on Actions column
                ]
            });
        });
    </script>
@endpush