@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Add New User
        </h1>
        <a href="{{ route('panel.users.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- User Creation Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('panel.users.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}" required placeholder="Enter full name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username" class="form-label">Username <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username" value="{{ old('username') }}" required
                                        placeholder="Enter username">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email') }}" placeholder="Enter email address">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}" placeholder="Enter phone number">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role" class="form-label">User Role <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role"
                                        required>
                                        <option value="">Select Role</option>
                                        <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>
                                            Cashier
                                        </option>
                                        <option value="educator" {{ old('role') == 'educator' ? 'selected' : '' }}>
                                            Educator
                                        </option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                            Admin IT
                                        </option>
                                        <option value="monitor" {{ old('role') == 'monitor' ? 'selected' : '' }}>
                                            Monitor
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Choose the appropriate role for this user
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required placeholder="Enter password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Password must be at least 8 characters long
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required placeholder="Confirm password">
                                    <small class="form-text text-muted">
                                        Re-enter the same password
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Role Information -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-left-info">
                                    <div class="card-body py-3">
                                        <h6 class="text-info">Role Descriptions:</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <i class="fas fa-cash-register fa-2x text-info mb-2"></i>
                                                    <h6 class="text-info">Cashier</h6>
                                                    <small class="text-muted">
                                                        Handle booking requests, manage tour reservations, and process
                                                        customer transactions
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <i class="fas fa-chalkboard-teacher fa-2x text-warning mb-2"></i>
                                                    <h6 class="text-warning">Educator</h6>
                                                    <small class="text-muted">
                                                        Manage tour sessions, view schedules, and monitor real-time session
                                                        capacity
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <i class="fas fa-user-shield fa-2x text-danger mb-2"></i>
                                                    <h6 class="text-danger">Admin IT</h6>
                                                    <small class="text-muted">
                                                        Full system access, user management, package configuration, and
                                                        system oversight
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <i class="fas fa-tv fa-2x text-primary mb-2"></i>
                                                    <h6 class="text-primary">Monitor</h6>
                                                    <small class="text-muted">
                                                        Display-only session monitor for visitors. No CRUD access.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('panel.users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Create User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Show role-specific information
            $('#role').change(function () {
                const role = $(this).val();

                // You can add role-specific UI changes here
                if (role === 'admin') {
                    $('#status').val('1'); // Auto-activate admin users
                }
            });

            // Password confirmation validation
            $('#password_confirmation').on('input', function () {
                const password = $('#password').val();
                const confirmation = $(this).val();

                if (confirmation && password !== confirmation) {
                    $(this).addClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                    $(this).after('<div class="invalid-feedback">Passwords do not match</div>');
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            // Username formatting
            $('#username').on('input', function () {
                // Convert to lowercase and remove spaces
                $(this).val($(this).val().toLowerCase().replace(/\s+/g, ''));
            });
        });
    </script>
@endpush