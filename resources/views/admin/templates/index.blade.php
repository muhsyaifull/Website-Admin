@extends('layouts.app')

@section('title', 'Session Templates')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-layer-group text-primary"></i> Tour Session Templates
        </h1>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Templates by Type -->
    @foreach(['taman' => 'Taman Atsiri', 'museum' => 'Museum Atsiri'] as $type => $typeLabel)
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold {{ $type == 'taman' ? 'text-success' : 'text-dark' }}">
                    <i class="fas {{ $type == 'taman' ? 'fa-seedling' : 'fa-building' }}"></i>
                    Template {{ $typeLabel }}
                </h6>
            </div>
            <div class="card-body">
                @php $typeTemplates = $templates->where('type', $type); @endphp

                @if($typeTemplates->isEmpty())
                    <p class="text-muted text-center my-3">No templates yet for {{ $typeLabel }}.</p>
                @else
                    <div class="row">
                        @foreach($typeTemplates as $template)
                            <div class="col-lg-6 mb-4">
                                <div
                                    class="card border-left-{{ $template->is_default ? 'primary' : ($template->is_active ? 'success' : 'secondary') }} shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="font-weight-bold mb-1">
                                                    {{ $template->name }}
                                                    @if($template->is_default)
                                                        <span class="badge badge-primary">Default</span>
                                                    @endif
                                                    @if(!$template->is_active)
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt"></i> {{ $template->apply_days_label }}
                                                </small>
                                            </div>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.templates.edit', $template) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.templates.toggle', $template) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-{{ $template->is_active ? 'success' : 'secondary' }}"
                                                        title="{{ $template->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i
                                                            class="fas {{ $template->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Delete this template?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @if($template->description)
                                            <p class="text-muted small mb-2">{{ $template->description }}</p>
                                        @endif

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Time</th>
                                                        <th>Capacity</th>
                                                        <th>Educator</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($template->slots as $slot)
                                                        <tr class="{{ !$slot->is_active ? 'text-muted' : '' }}">
                                                            <td><span class="badge badge-dark">{{ $slot->label }}</span></td>
                                                            <td>{{ $slot->capacity }} people</td>
                                                            <td>{{ $slot->educator ? $slot->educator->name : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Info:</strong> Tour sessions will be automatically created from active templates every day.
        Templates with specific days (e.g. Weekend) will be used on those days.
        Template <span class="badge badge-primary">Default</span> is used for days that don't have a specific
        template.
    </div>
@endsection