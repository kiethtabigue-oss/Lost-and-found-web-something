@extends('layouts.admin')

@section('title', 'Edit Account — ' . $account->name)
@section('page-title', 'Edit Community Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Account: {{ $account->name }}</h4>
                <p class="text-muted small mb-0">Modify user profile, reassign barangay jurisdiction, and manage status.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.accounts.show', $account) }}" class="btn btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> View Account
                </a>
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-gear text-primary me-2"></i>Account Information</h6>
                <span class="badge {{ $account->status === 'active' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border' }}">
                    {{ ucfirst($account->status) }}
                </span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.accounts.update', $account) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Role Selection --}}
                        <div class="col-md-6">
                            <label for="role_id" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $account->role_id) == $role->id ? 'selected' : '' }} data-slug="{{ $role->slug }}">
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Barangay Assignment --}}
                        <div class="col-md-6">
                            <label for="barangay_id" class="form-label fw-medium">Assigned Barangay</label>
                            <select class="form-select @error('barangay_id') is-invalid @enderror" id="barangay_id" name="barangay_id">
                                <option value="">None / Unassigned</option>
                                @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}" {{ old('barangay_id', $account->barangay_id) == $barangay->id ? 'selected' : '' }}>
                                    {{ $barangay->name }} ({{ $barangay->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('barangay_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Required for Barangay Administrators.</div>
                        </div>

                        {{-- Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $account->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $account->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-medium">Contact Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $account->phone) }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-medium">Account Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $account->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status', $account->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="deactivated" {{ old('status', $account->status) === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Password (Optional) --}}
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-semibold mb-2"><i class="bi bi-key text-warning me-2"></i>Change Password (Optional)</h6>
                            <p class="text-muted small">Leave both password fields blank if you do not wish to change the password.</p>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-medium">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Leave blank to keep existing" minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-medium">Confirm New Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" minlength="8">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span class="text-muted small">
                            <i class="bi bi-calendar me-1"></i> Registered on {{ $account->created_at->format('M d, Y') }}
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Update Account
                            </button>
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
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role_id');
    const barangaySelect = document.getElementById('barangay_id');

    function updateBarangayRequirement() {
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const slug = selectedOption ? selectedOption.getAttribute('data-slug') : '';

        if (slug === 'barangay_admin') {
            barangaySelect.setAttribute('required', 'required');
        } else {
            barangaySelect.removeAttribute('required');
        }
    }

    if (roleSelect && barangaySelect) {
        roleSelect.addEventListener('change', updateBarangayRequirement);
        updateBarangayRequirement();
    }
});
</script>
@endpush
