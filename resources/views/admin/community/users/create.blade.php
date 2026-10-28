@extends('layouts.admin')

@section('title', 'Create Community Account')
@section('page-title', 'Create Community Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-person-plus-fill text-primary me-2"></i>Create Community Account</h4>
                <p class="text-muted small mb-0">Register a new Barangay Administrator or community resident account.</p>
            </div>
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Accounts
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-lock text-primary me-2"></i>Account Details</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.accounts.store') }}">
                    @csrf

                    <div class="row g-3">
                        {{-- Role Selection --}}
                        <div class="col-md-6">
                            <label for="role_id" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $roles->firstWhere('slug', $defaultRole)?->id) == $role->id ? 'selected' : '' }}
                                    data-slug="{{ $role->slug }}">
                                    {{ $role->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Barangay Admins have moderation privileges within their assigned jurisdiction.</div>
                        </div>

                        {{-- Barangay Assignment --}}
                        <div class="col-md-6" id="barangayContainer">
                            <label for="barangay_id" class="form-label fw-medium">
                                Assigned Barangay <span class="text-danger" id="barangayRequiredAsterisk">*</span>
                            </label>
                            <select class="form-select @error('barangay_id') is-invalid @enderror" id="barangay_id" name="barangay_id">
                                <option value="">Select Barangay</option>
                                @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}" {{ old('barangay_id', $defaultBarangayId) == $barangay->id ? 'selected' : '' }}>
                                    {{ $barangay->name }} ({{ $barangay->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('barangay_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Specific barangay jurisdiction for moderation and reporting.</div>
                        </div>

                        {{-- Name --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g., Juan dela Cruz" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="e.g., juan@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-medium">Contact Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone') }}"
                                   placeholder="e.g., 09171234567">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-medium">Account Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="deactivated" {{ old('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-medium">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Minimum 8 characters" required minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-medium">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required minlength="8">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Create Account
                        </button>
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
    const asterisk = document.getElementById('barangayRequiredAsterisk');

    function updateBarangayRequirement() {
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const slug = selectedOption ? selectedOption.getAttribute('data-slug') : '';

        if (slug === 'barangay_admin') {
            if (asterisk) asterisk.style.display = 'inline';
            barangaySelect.setAttribute('required', 'required');
        } else {
            if (asterisk) asterisk.style.display = 'none';
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
