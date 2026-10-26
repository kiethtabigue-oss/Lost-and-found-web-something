<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecoveryClaim;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $query = Report::query();

        // Scope to barangay for barangay admins
        if ($user->isBarangayAdmin() && $user->barangay_id) {
            $query->where('barangay_id', $user->barangay_id);
        }

        $stats = [
            'total_users' => $user->isSuperAdmin()
                ? User::count()
                : User::where('barangay_id', $user->barangay_id)->count(),
            'active_users' => $user->isSuperAdmin()
                ? User::where('status', 'active')->count()
                : User::where('status', 'active')->where('barangay_id', $user->barangay_id)->count(),
            'total_lost_property' => (clone $query)->where('type', 'lost_property')->count(),
            'total_found_property' => (clone $query)->where('type', 'found_property')->count(),
            'total_missing_pets' => (clone $query)->where('type', 'missing_pet')->count(),
            'total_found_pets' => (clone $query)->where('type', 'found_pet')->count(),
            'pending_reports' => (clone $query)->where('status', 'pending_review')->count(),
            'active_reports' => (clone $query)->whereIn('status', ['approved', 'active', 'possible_match'])->count(),
            'recovered_reports' => (clone $query)->whereIn('status', ['recovered', 'reunited'])->count(),
            'pending_claims' => RecoveryClaim::where('status', 'pending')->count(),
        ];

        $stats['total_reports'] = $stats['total_lost_property'] + $stats['total_found_property']
            + $stats['total_missing_pets'] + $stats['total_found_pets'];

        $stats['recovery_rate'] = $stats['total_reports'] > 0
            ? round(($stats['recovered_reports'] / $stats['total_reports']) * 100, 1)
            : 0;

        $recentReports = (clone $query)->with(['user', 'category', 'barangay'])
            ->latest()
            ->take(10)
            ->get();

        $recentClaims = RecoveryClaim::with(['claimant', 'report'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports', 'recentClaims'));
    }
}
