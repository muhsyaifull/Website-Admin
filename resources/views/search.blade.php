@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-search text-primary"></i> Search Results
        </h1>
    </div>

    @if(strlen($q) < 2)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Please enter at least 2 characters to search.
        </div>
    @else
        <p class="text-muted mb-4">Result for: <strong>"{{ $q }}"</strong></p>

        @php
            $totalResults = collect($results)->sum(fn($items) => $items->count());
        @endphp

        @if($totalResults === 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No results found for "<strong>{{ $q }}</strong>".
            </div>
        @else
            {{-- Users (Admin only) --}}
            @if(isset($results['users']) && $results['users']->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users"></i> Users
                            <span class="badge badge-primary ml-2">{{ $results['users']->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['users'] as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td><span
                                                    class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'educator' ? 'success' : 'info') }}">{{ ucfirst($user->role) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('panel.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Packages --}}
            @if(isset($results['packages']) && $results['packages']->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-box"></i> Packages
                            <span class="badge badge-success ml-2">{{ $results['packages']->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Label</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['packages'] as $package)
                                        <tr>
                                            <td>{{ $package->name }}</td>
                                            <td><span class="badge badge-primary" style="color: white;">{{ $package->label }}</span>
                                            </td>
                                            <td>Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                                            <td>
                                                @if($package->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('panel.packages.edit', $package->id) }}"
                                                    class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Bookings --}}
            @if(isset($results['bookings']) && $results['bookings']->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-info">
                            <i class="fas fa-ticket-alt"></i> Reservations
                            <span class="badge badge-info ml-2">{{ $results['bookings']->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Reservation Code</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Participants</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['bookings'] as $booking)
                                        <tr>
                                            <td><strong>{{ $booking->booking_code }}</strong></td>
                                            <td>{{ $booking->representative_name }}</td>
                                            <td>{{ $booking->representative_phone }}</td>
                                            <td>{{ $booking->total_participants }}</td>
                                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            <td>
                                                @if(auth()->user()->role === 'cashier')
                                                    <a href="{{ route('kasir.booking.show', $booking->id) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('panel.bookings.show', $booking->id) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Educators --}}
            @if(isset($results['educators']) && $results['educators']->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-chalkboard-teacher"></i> Educators
                            <span class="badge badge-warning ml-2">{{ $results['educators']->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Specialization</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['educators'] as $educator)
                                        <tr>
                                            <td>{{ $educator->name }}</td>
                                            <td>{{ $educator->phone ?? '-' }}</td>
                                            <td>
                                                @if($educator->tours && $educator->tours->count() > 0)
                                                    @foreach($educator->tours as $tour)
                                                        <span class="badge badge-primary">{{ $tour->name }}</span>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($educator->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(auth()->user()->role === 'admin')
                                                    <a href="{{ route('panel.educators.show', $educator->id) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif
@endsection