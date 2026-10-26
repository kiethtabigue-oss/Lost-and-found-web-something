<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\PossibleMatch;
use App\Models\RecoveryClaim;
use App\Models\Report;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $myReports = Report::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_reports' => Report::where('user_id', $user->id)->count(),
            'active_reports' => Report::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'active', 'possible_match', 'claim_pending'])
                ->count(),
            'recovered_reports' => Report::where('user_id', $user->id)
                ->whereIn('status', ['recovered', 'reunited'])
                ->count(),
            'pending_claims' => RecoveryClaim::where('claimant_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'possible_matches' => PossibleMatch::whereHas('sourceReport', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'pending')->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        return view('resident.dashboard', compact('stats', 'myReports'));
    }
}
