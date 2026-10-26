<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportStatusHistory;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Report::with(['user', 'category', 'barangay', 'images']);

        // Scope for barangay admins
        if ($user->isBarangayAdmin() && $user->barangay_id) {
            $query->where('barangay_id', $user->barangay_id);
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('barangay_id') && $user->isSuperAdmin()) {
            $query->where('barangay_id', $request->input('barangay_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('incident_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('incident_date', '<=', $request->input('date_to'));
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function pending(Request $request): View
    {
        $user = auth()->user();

        $query = Report::with(['user', 'category', 'barangay', 'images'])
            ->where('status', ReportStatus::PendingReview);

        if ($user->isBarangayAdmin() && $user->barangay_id) {
            $query->where('barangay_id', $user->barangay_id);
        }

        $reports = $query->oldest()->paginate(15)->withQueryString();

        return view('admin.reports.pending', compact('reports'));
    }

    public function show(Report $report): View
    {
        $user = auth()->user();

        if ($user->isBarangayAdmin() && $report->barangay_id !== $user->barangay_id) {
            abort(403, 'You can only view reports in your barangay.');
        }

        $report->load([
            'user.profile', 'category', 'barangay', 'communityZone', 'images',
            'lostPropertyDetail', 'foundPropertyDetail',
            'missingPetDetail', 'foundPetDetail',
            'statusHistories.changedByUser',
            'claims.claimant', 'flags',
            'possibleMatches.matchedReport',
        ]);

        return view('admin.reports.show', compact('report'));
    }

    public function approve(Report $report): RedirectResponse
    {
        $this->authorizeManagement($report);

        if ($report->status !== ReportStatus::PendingReview) {
            return back()->with('error', 'Only pending reports can be approved.');
        }

        $this->transitionStatus($report, ReportStatus::Active, 'Report approved by administrator.');

        $matches = (new MatchingService)->findMatchesFor($report);
        $message = "Report {$report->reference_number} has been approved.";
        if ($matches > 0) {
            $message .= " System found {$matches} potential match(es)!";
        }

        return back()->with('success', $message);
    }

    public function reject(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeManagement($report);

        if ($report->status !== ReportStatus::PendingReview) {
            return back()->with('error', 'Only pending reports can be rejected.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $this->transitionStatus($report, ReportStatus::Rejected, $request->input('rejection_reason'));

        return back()->with('success', "Report {$report->reference_number} has been rejected.");
    }

    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeManagement($report);

        $request->validate([
            'new_status' => ['required', 'string'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = ReportStatus::from($request->input('new_status'));

        if (! $report->status->canTransitionTo($newStatus)) {
            return back()->with('error', "Cannot transition from {$report->status->label()} to {$newStatus->label()}.");
        }

        $remarks = (string) ($request->input('remarks') ?? '');
        $this->transitionStatus($report, $newStatus, $remarks);

        if ($newStatus === ReportStatus::Active) {
            (new MatchingService)->findMatchesFor($report);
        }

        return back()->with('success', "Report status updated to {$newStatus->label()}.");
    }

    private function transitionStatus(Report $report, ReportStatus $newStatus, ?string $remarks = ''): void
    {
        $oldStatus = $report->status;
        $remarks = $remarks ?? '';

        $report->update([
            'status' => $newStatus,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
            'moderation_notes' => $remarks,
        ]);

        ReportStatusHistory::create([
            'report_id' => $report->id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'changed_by' => auth()->id(),
            'remarks' => $remarks,
        ]);

        AuditLog::record('report_status_changed', $report, ['status' => $oldStatus->value], ['status' => $newStatus->value]);
    }

    private function authorizeManagement(Report $report): void
    {
        $user = auth()->user();

        if ($user->isBarangayAdmin() && $report->barangay_id !== $user->barangay_id) {
            abort(403, 'You can only manage reports in your barangay.');
        }
    }
}
