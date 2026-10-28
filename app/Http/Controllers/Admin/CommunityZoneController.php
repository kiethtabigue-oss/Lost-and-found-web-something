<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\CommunityZone;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommunityZoneController extends Controller
{
    /**
     * Store a newly created zone for a barangay.
     */
    public function store(Request $request, Barangay $barangay): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['barangay_id'] = $barangay->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        $zone = CommunityZone::create($validated);

        AuditLog::record(
            action: 'create_community_zone',
            auditable: $zone,
            oldValues: null,
            newValues: $zone->only(['barangay_id', 'name', 'description', 'is_active'])
        );

        return redirect()->route('admin.barangays.show', $barangay)
            ->with('success', "Community Zone '{$zone->name}' was added to {$barangay->name}.");
    }

    /**
     * Update the specified community zone.
     */
    public function update(Request $request, CommunityZone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $oldValues = $zone->only(['name', 'description', 'is_active']);
        $zone->update($validated);
        $newValues = $zone->only(['name', 'description', 'is_active']);

        AuditLog::record(
            action: 'update_community_zone',
            auditable: $zone,
            oldValues: $oldValues,
            newValues: $newValues
        );

        return back()->with('success', "Zone '{$zone->name}' updated successfully.");
    }

    /**
     * Toggle the active status of the specified community zone.
     */
    public function toggleStatus(CommunityZone $zone): RedirectResponse
    {
        $oldStatus = $zone->is_active;
        $zone->is_active = ! $oldStatus;
        $zone->save();

        AuditLog::record(
            action: 'toggle_community_zone_status',
            auditable: $zone,
            oldValues: ['is_active' => $oldStatus],
            newValues: ['is_active' => $zone->is_active]
        );

        $statusText = $zone->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Zone '{$zone->name}' has been {$statusText}.");
    }

    /**
     * Remove the specified community zone.
     */
    public function destroy(CommunityZone $zone): RedirectResponse
    {
        $linkedReports = Report::where('community_zone_id', $zone->id)->count();

        if ($linkedReports > 0) {
            return back()->with('error', "Cannot delete zone '{$zone->name}' because {$linkedReports} report(s) are assigned to it. Deactivate it instead.");
        }

        $oldData = $zone->toArray();
        $zoneName = $zone->name;

        $zone->delete();

        AuditLog::record(
            action: 'delete_community_zone',
            auditable: null,
            oldValues: $oldData,
            newValues: null
        );

        return back()->with('success', "Zone '{$zoneName}' has been removed successfully.");
    }
}
