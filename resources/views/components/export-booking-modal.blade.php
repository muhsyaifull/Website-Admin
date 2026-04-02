<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportBookingModal">
    <i class="fas fa-file-excel mr-1"></i> Export Excel
</button>

<div class="modal fade" id="exportBookingModal" tabindex="-1" role="dialog" aria-labelledby="exportBookingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportBookingModalLabel">
                    <i class="fas fa-file-excel mr-2"></i> Export Data Resesrvation
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('panel.bookings.export') }}" method="GET">
                <div class="modal-body">

                    {{-- Visit of Period Filter --}}
                    <div class="form-group">
                        <label class="font-weight-bold mb-1">
                            <i class="fas fa-calendar-alt mr-1"></i> Period of Visit
                        </label>
                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted">From</label>
                                <input type="date" name="start_date" class="form-control form-control-sm"
                                    value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted">To</label>
                                <input type="date" name="end_date" class="form-control form-control-sm"
                                    value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <small class="text-muted">Empty both to export all periods.</small>
                    </div>

                    {{-- Filter Tour --}}
                    <div class="form-group">
                        <label class="font-weight-bold mb-1">
                            <i class="fas fa-map-marked-alt mr-1"></i> Tour
                        </label>
                        <select name="tour_id" class="form-control form-control-sm">
                            <option value="">— All Tour —</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="form-group mb-0">
                        <label class="font-weight-bold mb-1">
                            <i class="fas fa-filter mr-1"></i> Reservation Status
                        </label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">— All Status —</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-download mr-1"></i> Download Excel
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>