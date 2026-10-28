@extends('layouts.guest')

@section('title', 'Login — Community Recovery System')

@section('content')
@include('auth.partials.flashlight-styles')

<div class="lf" id="lfRoot">
    @include('auth.partials.flashlight-stage')

    <div class="lf-card">
        <div class="text-center mb-4">
            <span class="lf-mark" aria-hidden="true"><i class="bi bi-shield-check"></i></span>
            <h1>Welcome <span>Back</span></h1>
            <p class="lf-sub">Sign in to the Community Recovery System</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <small><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</small>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <small><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</small>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-medium">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}"
                           placeholder="your@email.com" required autofocus>
                </div>
                @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-medium mb-0">Password</label>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" placeholder="Enter your password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn"
                            aria-label="Show password" aria-pressed="false">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="remember">Keep me logged in</label>
                </div>
            </div>

            <button type="submit" class="btn lf-submit w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>

        {{-- Quick Demo Credentials Pill Bar --}}
        {{-- <div class="lf-demo mt-4 pt-3 text-center">
            <span class="lf-demo-title fw-semibold d-block mb-2">Quick demo fill</span>
            <div class="d-flex flex-wrap justify-content-center gap-1">
                <button type="button" class="btn btn-sm py-1 px-2 demo-fill-btn"
                        data-email="superadmin@community-recovery.test" data-password="password123">
                    <i class="bi bi-shield-shaded me-1 text-primary"></i>Super Admin
                </button>
                <button type="button" class="btn btn-sm py-1 px-2 demo-fill-btn"
                        data-email="admin.sanisidro@community-recovery.test" data-password="password123">
                    <i class="bi bi-building me-1 text-info"></i>Brgy Admin
                </button>
                <button type="button" class="btn btn-sm py-1 px-2 demo-fill-btn"
                        data-email="juan@community-recovery.test" data-password="password123">
                    <i class="bi bi-person me-1 text-success"></i>Resident
                </button>
            </div>
        </div> --}}

        <p class="lf-register text-center small mt-4 mb-0">
            Don't have an account?
            <a href="{{ route('register') }}">Register here</a>
        </p>
    </div>
</div>

@include('auth.partials.flashlight-script')

<script>
(() => {
    // /* ------------------------------------------------------------------
    //    Auth form behaviour (unchanged functionality)
    //    ------------------------------------------------------------------ */
    // document.querySelectorAll('.demo-fill-btn').forEach(btn => {
    //     btn.addEventListener('click', function () {
    //         document.getElementById('email').value = this.dataset.email;
    //         document.getElementById('password').value = this.dataset.password;
    //     });
    // });

    const toggleBtn = document.getElementById('togglePasswordBtn');
    toggleBtn?.addEventListener('click', function () {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('i');
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        icon.classList.replace(show ? 'bi-eye' : 'bi-eye-slash', show ? 'bi-eye-slash' : 'bi-eye');
        this.setAttribute('aria-pressed', String(show));
        this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });

})();
</script>
@endsection