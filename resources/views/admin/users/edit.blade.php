@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit text-primary"></i> Edit User
        </h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- User Edit Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit User Information</h6>
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

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $user->name) }}" required placeholder="Enter full name">
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
                                        id="username" name="username" value="{{ old('username', $user->username) }}" required
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
                                        name="email" value="{{ old('email', $user->email) }}" placeholder="Enter email address">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number">
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
                                        <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>
                                            Cashier
                                        </option>
                                        <option value="educator" {{ old('role', $user->role) == 'educator' ? 'selected' : '' }}>
                                            Educator
                                        </option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                            Admin IT
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
                                    <div class="form-check mt-4">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" 
                                               id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            User Active
                                        </label>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Uncheck to deactivate this user
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section (Optional for editing) -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-warning mt-3">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="m-0"><i class="fas fa-lock"></i> Change Password (Optional)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password" class="form-label">New Password</label>
                                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" placeholder="Leave blank to keep current password">
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
                                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="password_confirmation"
                                                        name="password_confirmation" placeholder="Confirm new password">
                                                    <small class="form-text text-muted">
                                                        Re-enter the new password to confirm
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection