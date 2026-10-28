<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Barangay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangayController extends Controller
{
    /**
     * Display a listing of barangays with community statistics.
     */
    public function index(Request $request): View
    {
        $query = Barangay::withCount(['zones', 'users', 'admins', 'residents', 'reports']);

        // Search filter
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $barangays = $query->orderBy('name')->paginate(12)->withQueryString();

        $metrics = [
            'total' => Barangay::count(),
            'active' => Barangay::where('is_active', true)->count(),
            'inactive' => Barangay::where('is_active', false)->count(),
            'total_zones' => \App\Models\CommunityZone::count(),
            'total_admins' => \App\Models\User::whereHas('role', fn ($q) => $q->where('slug', 'barangay_admin'))->count(),
        ];

        return view('admin.community.barangays.index', compact('barangays', 'metrics'));
    }

    /**
     * Show the form for creating a new barangay.
     */
    public function create(): View
    {
        return view('admin.community.barangays.create');
    }

    /**
     * Store a newly created barangay in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:barangays,code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $barangay = Barangay::create($validated);

        AuditLog::record(
            action: 'create_barangay',
            auditable: $barangay,
            oldValues: null,
            newValues: $barangay->only(['name', 'code', 'description', 'latitude', 'longitude', 'is_active'])
        );

        return redirect()->route('admin.barangays.show', $barangay)
            ->with('success', "Barangay {$barangay->name} ({$barangay->code}) was created successfully.");
    }

    /**
     * Display the specified barangay with zones, admins, and reports.
     */
    public function show(Barangay $barangay): View
    {
        $barangay->loadCount(['zones', 'users', 'admins', 'residents', 'reports']);

        $zones = $barangay->zones()->orderBy('name')->get();

        $admins = $barangay->admins()->with('profile')->latest()->get();

        $recentReports = $barangay->reports()
            ->with(['user', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $stats = [
            'total_reports' => $barangay->reports()->count(),
            'pending_reports' => $barangay->reports()->where('status', 'pending_review')->count(),
            'recovered_reports' => $barangay->reports()->whereIn('status', ['recovered', 'reunited'])->count(),
            'total_admins' => $barangay->admins()->count(),
            'total_residents' => $barangay->residents()->count(),
            'total_zones' => $barangay->zones()->count(),
        ];

        return view('admin.community.barangays.show', compact('barangay', 'zones', 'admins', 'recentReports', 'stats'));
    }

    /**
     * Show the form for editing the specified barangay.
     */
    public function edit(Barangay $barangay): View
    {
        return view('admin.community.barangays.edit', compact('barangay'));
    }

    /**
     * Update the specified barangay in storage.
     */
    public function update(Request $request, Barangay $barangay): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('barangays', 'code')->ignore($barangay->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $oldValues = $barangay->only(['name', 'code', 'description', 'latitude', 'longitude', 'is_active']);

        $barangay->update($validated);

        $newValues = $barangay->only(['name', 'code', 'description', 'latitude', 'longitude', 'is_active']);

        AuditLog::record(
            action: 'update_barangay',
            auditable: $barangay,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return redirect()->route('admin.barangays.show', $barangay)
            ->with('success', "Barangay {$barangay->name} was updated successfully.");
    }

    /**
     * Toggle the active status of the specified barangay.
     */
    public function toggleStatus(Barangay $barangay): RedirectResponse
    {
        $oldStatus = $barangay->is_active;
        $barangay->is_active = ! $oldStatus;
        $barangay->save();

        AuditLog::record(
            action: 'toggle_barangay_status',
            auditable: $barangay,
            oldValues: ['is_active' => $oldStatus],
            newValues: ['is_active' => $barangay->is_active]
        );

        $statusText = $barangay->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Barangay {$barangay->name} has been {$statusText}.");
    }

    /**
     * Remove the specified barangay from storage.
     */
    public function destroy(Barangay $barangay): RedirectResponse
    {
        if (! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Administrators can permanently delete a barangay jurisdiction.');
        }

        $reportsCount = $barangay->reports()->count();
        $usersCount = $barangay->users()->count();

        if ($reportsCount > 0 || $usersCount > 0) {
            return back()->with('error', "Cannot delete {$barangay->name} because it is linked to {$reportsCount} report(s) and {$usersCount} account(s). You can deactivate it instead.");
        }

        $oldData = $barangay->toArray();
        $barangayName = $barangay->name;

        $barangay->delete();

        AuditLog::record(
            action: 'delete_barangay',
            auditable: null,
            oldValues: $oldData,
            newValues: null
        );

        return redirect()->route('admin.barangays.index')
            ->with('success', "Barangay {$barangayName} has been deleted successfully.");
    }
}
