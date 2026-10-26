<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Too many login attempts. Please try again in {$seconds} seconds."]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        $passwordMatches = false;

        if ($user) {
            $passwordMatches = ($user->password === $credentials['password'])
                || Hash::check($credentials['password'], $user->password);
        }

        if ($user && $passwordMatches) {
            if (! $user->isActive()) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Your account has been suspended. Please contact an administrator.']);
            }

            Auth::login($user, $request->boolean('remember'));

            RateLimiter::clear($key);
            $request->session()->regenerate();

            AuditLog::record('login', $user);

            return $this->redirectByRole($user);
        }

        RateLimiter::hit($key, 60);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function showRegister(): View
    {
        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();

        return view('auth.register', compact('barangays'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'barangay_id' => ['required', 'exists:barangays,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'terms' => ['accepted'],
        ]);

        $residentRole = Role::where('slug', 'resident')->firstOrFail();

        $user = DB::transaction(function () use ($validated, $residentRole) {
            $user = User::create([
                'role_id' => $residentRole->id,
                'barangay_id' => $validated['barangay_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);

            UserProfile::create(['user_id' => $user->id]);

            AuditLog::record('register', $user, null, ['email' => $user->email], $user->id);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('resident.dashboard')
            ->with('success', 'Registration successful! Welcome to the Community Recovery System.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::record('logout', $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->isSuperAdmin() || $user->isBarangayAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('resident.dashboard');
    }
}
