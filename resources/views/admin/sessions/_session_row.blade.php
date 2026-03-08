<tr>
    <td>
        <div class="font-weight-bold">{{ $session->date->format('d M Y') }}</div>
        <small class="text-muted">{{ $session->date->translatedFormat('l') }}</small>
    </td>
    <td>
        <span class="badge badge-dark">{{ $session->label }}</span>
    </td>
    <td>
        <div class="font-weight-bold">{{ $session->educator->name }}</div>
    </td>
    <td>
        @php
            $percentage = $session->capacity > 0 ? round(($session->booked / $session->capacity) * 100) : 0;
            $barColor = $session->is_full ? 'bg-danger' : ($session->is_low ? 'bg-warning' : 'bg-success');
        @endphp
        <div class="progress" style="height: 20px;">
            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $percentage }}%"
                aria-valuenow="{{ $session->booked }}" aria-valuemin="0" aria-valuemax="{{ $session->capacity }}">
                {{ $session->booked }}/{{ $session->capacity }}
            </div>
        </div>
        <small class="text-muted">Available {{ $session->available }}</small>
    </td>
    <td>
        @if($session->is_full)
            <span class="badge badge-danger">Full</span>
        @elseif(!$session->is_active)
            <span class="badge badge-secondary">Inactive</span>
        @elseif($session->is_low)
            <span class="badge badge-warning">Almost Full</span>
        @else
            <span class="badge badge-success">Available</span>
        @endif
    </td>
    <td>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.sessions.edit', $session->id) }}" class="btn btn-sm btn-outline-warning"
                title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @if($session->booked == 0)
                <form action="{{ route('admin.sessions.destroy', $session->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete this session?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>