@extends('layouts.app')

@section('title', 'Edit Session Template')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary"></i> Edit Template: {{ $template->name }}
        </h1>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.templates.update', $template) }}" method="POST" id="templateForm">
        @csrf
        @method('PUT')

        <!-- Template Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Template Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type">Tour Type</label>
                            <input type="text" class="form-control" value="{{ $template->tour_type_label }}" disabled>
                            <small class="text-muted">Type cannot be changed.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="is_default">Set as Default</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default"
                                    value="1" {{ old('is_default', $template->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    Default template for this type
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $template->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Apply on Days</label>
                            <div class="d-flex flex-wrap">
                                @php
                                    $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $currentDays = old('apply_days', $template->apply_days ?? []);
                                @endphp
                                @foreach($dayNames as $i => $day)
                                    <div class="form-check mr-3 mb-1">
                                        <input class="form-check-input" type="checkbox" name="apply_days[]"
                                            value="{{ $i }}" id="day_{{ $i }}"
                                            {{ in_array($i, $currentDays) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $i }}">{{ $day }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Leave empty if only used manually.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Slots -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Time Slots</h6>
                <button type="button" class="btn btn-sm btn-success" id="addSlot">
                    <i class="fas fa-plus"></i> Add Slot
                </button>
            </div>
            <div class="card-body">
                <div id="slotsContainer"></div>
                <div id="noSlotsMsg" class="text-center text-muted py-3" style="display: none;">
                    No time slots yet.
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    let slotIndex = 0;
    const educators = @json($educators);
    const existingSlots = @json($template->slots);

    function formatTime(timeStr) {
        if (!timeStr) return '';
        // Handle "HH:mm:ss" or "YYYY-MM-DD HH:mm:ss" formats
        const match = timeStr.match(/(\d{2}):(\d{2})/);
        return match ? match[1] + ':' + match[2] : '';
    }

    function createSlotRow(data = {}) {
        const index = slotIndex++;
        const educatorOptions = educators.map(e =>
            `<option value="${e.id}" ${data.educator_id == e.id ? 'selected' : ''}>${e.name}</option>`
        ).join('');

        const slotId = data.id || '';
        const startTime = formatTime(data.start_time) || '';
        const endTime = formatTime(data.end_time) || '';

        const html = `
            <div class="slot-row card border mb-3" data-index="${index}">
                <div class="card-body py-3">
                    <div class="row align-items-end">
                        ${slotId ? `<input type="hidden" name="slots[${index}][id]" value="${slotId}">` : ''}
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small">Start <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="slots[${index}][start_time]"
                                    value="${startTime}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small">End <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="slots[${index}][end_time]"
                                    value="${endTime}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="slots[${index}][capacity]"
                                    value="${data.capacity || 20}" min="1" max="50" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small">Educator</label>
                                <select class="form-control" name="slots[${index}][educator_id]">
                                    <option value="">-- Auto --</option>
                                    ${educatorOptions}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-slot" title="Remove slot">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('slotsContainer').insertAdjacentHTML('beforeend', html);
        toggleNoSlotsMsg();
    }

    function toggleNoSlotsMsg() {
        const count = document.querySelectorAll('.slot-row').length;
        document.getElementById('noSlotsMsg').style.display = count === 0 ? 'block' : 'none';
    }

    document.getElementById('addSlot').addEventListener('click', () => createSlotRow());

    document.getElementById('slotsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-slot')) {
            e.target.closest('.slot-row').remove();
            toggleNoSlotsMsg();
        }
    });

    // Load existing slots
    existingSlots.forEach(slot => createSlotRow(slot));
    if (existingSlots.length === 0) createSlotRow({ capacity: 20 });
</script>
@endpush
