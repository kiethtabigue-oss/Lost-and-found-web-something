<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class BarangayAccountController extends Controller
{
    /**
     * Display a listing of community accounts (focused on Barangay Admins and Residents).
     */
    public function index(Request $request): View
    {
        $currentRoleTab = $request->input('role', UserRole::BarangayAdmin->value);

        $query = User::with(['role', 'barangay', 'profile'])
            ->withCount(['reports', 'claims']);

        // Tab role filter
        if ($currentRoleTab && $currentRoleTab !== 'all') {
            $query->whereHas('role', fn ($q) => $q->where('slug', $currentRoleTab));
        }

        // Barangay filter
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->input('barangay_id'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search filter
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $barangays = Barangay::orderBy('name')->get();
        $roles = Role::orderBy('id')->get();

        $metrics = [
            'total_admins' => User::whereHas('role', fn ($q) => $q->where('slug', UserRole::BarangayAdmin->value))->count(),
            'active_admins' => User::whereHas('role', fn ($q) => $q->where('slug', UserRole::BarangayAdmin->value))->where('status', 'active')->count(),
            'suspended_admins' => User::whereHas('role', fn ($q) => $q->where('slug', UserRole::BarangayAdmin->value))->where('status', 'suspended')->count(),
            'total_residents' => User::whereHas('role', fn ($q) => $q->where('slug', UserRole::Resident->value))->count(),
            'total_all' => User::count(),
        ];

        return view('admin.community.users.index', compact('users', 'barangays', 'roles', 'metrics', 'currentRoleTab'));
    }

    /**
     * Show the form for creating a new community/barangay account.
     */
    public function create(Request $request): View
    {
        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();
        $roles = Role::all();
        $defaultBarangayId = $request->query('barangay_id');
        $defaultRole = $request->query('role', UserRole::BarangayAdmin->value);

        return view('admin.community.users.create', compact('barangays', 'roles', 'defaultBarangayId', 'defaultRole'));
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'barangay_id' => ['nullable', 'exists:barangays,id'],
            'status' => ['required', 'in:active,suspended,deactivated'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        // Barangay Admin must be assigned to a barangay
        if ($role->slug === UserRole::BarangayAdmin->value && empty($validated['barangay_id'])) {
            return back()->withInput()->withErrors([
                'barangay_id' => 'Barangay Administrators must be assigned to a specific community barangay.',
            ]);
        }

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'role_id' => $validated['role_id'],
                'barangay_id' => $validated['barangay_id'] ?? null,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => $validated['password'],
                'status' => $validated['status'],
                'email_verified_at' => now(),
            ]);

            UserProfile::create(['user_id' => $user->id]);

            AuditLog::record(
                action: 'create_user_account',
                auditable: $user,
                oldValues: null,
                newValues: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'barangay_id' => $user->barangay_id,
                    'status' => $user->status,
                ]
            );

            return $user;
        });

        return redirect()->route('admin.accounts.index', ['role' => $role->slug])
            ->with('success', "Account for {$user->name} was created successfully.");
    }

    /**
     * Display the specified account.
     */
    public function show(User $account): View
    {
        $account->load(['role', 'barangay', 'profile']);
        $account->loadCount(['reports', 'claims']);

        $recentReports = $account->reports()->with('category')->latest()->take(6)->get();
        $auditLogs = AuditLog::where('user_id', $account->id)
            ->orWhere(function ($q) use ($account) {
                $q->where('auditable_type', User::class)->where('auditable_id', $account->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('admin.community.users.show', compact('account', 'recentReports', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(User $account): View
    {
        $barangays = Barangay::orderBy('name')->get();
        $roles = Role::all();

        return view('admin.community.users.edit', compact('account', 'barangays', 'roles'));
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, User $account): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($account->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'barangay_id' => ['nullable', 'exists:barangays,id'],
            'status' => ['required', 'in:active,suspended,deactivated'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $role = Role::findOrFail($validated['role_id']);

        // Barangay Admin must have a barangay
        if ($role->slug === UserRole::BarangayAdmin->value && empty($validated['barangay_id'])) {
            return back()->withInput()->withErrors([
                'barangay_id' => 'Barangay Administrators must be assigned to a specific community barangay.',
            ]);
        }

        // Prevent self-demotion or self-suspension
        if ($account->id === auth()->id()) {
            if ($validated['status'] !== 'active') {
                return back()->withInput()->withErrors([
                    'status' => 'You cannot suspend or deactivate your own active session account.',
                ]);
            }
        }

        $oldValues = $account->only(['name', 'email', 'phone', 'role_id', 'barangay_id', 'status']);

        $account->name = $validated['name'];
        $account->email = $validated['email'];
        $account->phone = $validated['phone'] ?? null;
        $account->role_id = $validated['role_id'];
        $account->barangay_id = $validated['barangay_id'] ?? null;
        $account->status = $validated['status'];

        if (! empty($validated['password'])) {
            $account->password = $validated['password'];
        }

        $account->save();

        $newValues = $account->only(['name', 'email', 'phone', 'role_id', 'barangay_id', 'status']);

        AuditLog::record(
            action: 'update_user_account',
            auditable: $account,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return redirect()->route('admin.accounts.index', ['role' => $role->slug])
            ->with('success', "Account for {$account->name} updated successfully.");
    }

    /**
     * Toggle the status between active and suspended.
     */
    public function toggleStatus(User $account): RedirectResponse
    {
        if ($account->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $oldStatus = $account->status;
        $newStatus = ($oldStatus === 'active') ? 'suspended' : 'active';

        $account->status = $newStatus;
        $account->save();

        AuditLog::record(
            action: 'toggle_user_status',
            auditable: $account,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $newStatus]
        );

        $actionText = $newStatus === 'active' ? 'activated' : 'suspended';

        return back()->with('success', "Account for {$account->name} has been {$actionText}.");
    }

    /**
     * Reset the password for the specified account.
     */
    public function resetPassword(Request $request, User $account): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        $account->password = $validated['new_password'];
        $account->save();

        AuditLog::record(
            action: 'admin_reset_password',
            auditable: $account,
            oldValues: null,
            newValues: ['password_reset' => true]
        );

        return back()->with('success', "Password for {$account->name} has been successfully reset.");
    }

    /**
     * Soft delete the specified user account.
     */
    public function destroy(User $account): RedirectResponse
    {
        if ($account->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $oldData = $account->only(['id', 'name', 'email', 'role_id', 'barangay_id']);
        $userName = $account->name;

        $account->delete();

        AuditLog::record(
            action: 'delete_user_account',
            auditable: null,
            oldValues: $oldData,
            newValues: null
        );

        return back()->with('success', "Account for {$userName} has been deleted.");
    }
}
