@extends('layouts.guest')

@section('title', 'Register — Community Recovery System')

@section('content')
@include('auth.partials.flashlight-styles')

<div class="lf" id="lfRoot">
    @include('auth.partials.flashlight-stage')

    <div class="lf-card lf-card--wide">
        <div class="text-center mb-4">
            <span class="lf-mark" aria-hidden="true"><i class="bi bi-shield-check"></i></span>
            <h1>Create an <span>Account</span></h1>
            <p class="lf-sub">Join the Community Recovery System</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-medium">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}"
                           placeholder="Juan Dela Cruz" required autofocus>
                </div>
                @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}"
                           placeholder="your@email.com" required>
                </div>
                @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="barangay_id" class="form-label fw-medium">Barangay</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                    <select class="form-select @error('barangay_id') is-invalid @enderror"
                            id="barangay_id" name="barangay_id" required>
                        <option value="">Select your barangay</option>
                        @foreach($barangays as $barangay)
                        <option value="{{ $barangay->id }}" {{ old('barangay_id') == $barangay->id ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @error('barangay_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-medium">Phone Number <span class="text-muted">(optional)</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="09171234567">
                </div>
                @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="password" class="form-label fw-medium">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" placeholder="Min 8 characters" required>
                    </div>
                    @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-medium">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="Confirm password" required>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror"
                           type="checkbox" name="terms" id="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="terms">
                        I agree to the <a href="#" class="text-decoration-none">Terms of Service</a>
                        and <a href="#" class="text-decoration-none">Privacy Policy</a>
                    </label>
                    @error('terms')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn lf-submit w-100 py-2 fw-semibold">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </form>

        <hr class="my-4">

        <p class="lf-register text-center small mb-0">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</div>

@include('auth.partials.flashlight-script')
@endsection