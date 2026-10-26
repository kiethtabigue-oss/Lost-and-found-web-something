<?php

namespace App\Http\Controllers\Resident;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\AuditLog;
use App\Models\Barangay;
use App\Models\CommunityZone;
use App\Models\FoundPetDetail;
use App\Models\FoundPropertyDetail;
use App\Models\LostPropertyDetail;
use App\Models\MissingPetDetail;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportImage;
use App\Models\ReportStatusHistory;
use App\Services\MatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Report::where('user_id', $user->id)
            ->with(['category', 'barangay', 'images']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(10)->withQueryString();

        return view('resident.reports.index', compact('reports'));
    }

    public function create(Request $request): View
    {
        $type = $request->query('type', 'lost_property');
        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();
        $categories = ReportCategory::where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $zones = CommunityZone::where('is_active', true)->orderBy('name')->get();

        return view('resident.reports.create', compact('type', 'barangays', 'categories', 'zones'));
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = auth()->user();

        $report = DB::transaction(function () use ($validated, $request, $user) {
            // Create the report
            $report = Report::create([
                'user_id' => $user->id,
                'category_id' => $validated['category_id'],
                'barangay_id' => $validated['barangay_id'],
                'community_zone_id' => $validated['community_zone_id'] ?? null,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'incident_date' => $validated['incident_date'],
                'incident_time' => $validated['incident_time'] ?? null,
                'location_description' => $validated['location_description'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => ReportStatus::PendingReview,
                'contact_preference' => $validated['contact_preference'],
                'is_public' => $validated['is_public'] ?? true,
            ]);

            // Create type-specific details
            $this->createReportDetails($report, $validated);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->handleImageUploads($report, $request->file('images'));
            }

            // Log status history
            ReportStatusHistory::create([
                'report_id' => $report->id,
                'old_status' => null,
                'new_status' => ReportStatus::PendingReview->value,
                'changed_by' => $user->id,
                'remarks' => 'Report submitted by resident.',
            ]);

            AuditLog::record('report_created', $report, null, [
                'type' => $report->type->value,
                'title' => $report->title,
            ]);

            return $report;
        });

        $matches = (new MatchingService)->findMatchesFor($report);
        $msg = "Report submitted successfully! Reference: {$report->reference_number}";
        if ($matches > 0) {
            $msg .= " We found {$matches} possible matching report(s)!";
        }

        return redirect()->route('resident.reports.show', $report)
            ->with('success', $msg);
    }

    public function show(Report $report): View
    {
        $this->authorizeOwnership($report);

        $report->load([
            'category', 'barangay', 'communityZone', 'images',
            'lostPropertyDetail', 'foundPropertyDetail',
            'missingPetDetail', 'foundPetDetail',
            'statusHistories.changedByUser',
            'claims.claimant',
            'possibleMatches.matchedReport.images',
        ]);

        return view('resident.reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        $this->authorizeOwnership($report);

        if (! in_array($report->status, [ReportStatus::Draft, ReportStatus::PendingReview, ReportStatus::Rejected])) {
            return redirect()->route('resident.reports.show', $report)
                ->with('error', 'This report can no longer be edited.');
        }

        $report->load(['lostPropertyDetail', 'foundPropertyDetail', 'missingPetDetail', 'foundPetDetail', 'images']);

        $barangays = Barangay::where('is_active', true)->orderBy('name')->get();
        $categories = ReportCategory::where('type', $report->type->value)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $zones = CommunityZone::where('is_active', true)->orderBy('name')->get();

        return view('resident.reports.edit', compact('report', 'barangays', 'categories', 'zones'));
    }

    public function update(StoreReportRequest $request, Report $report): RedirectResponse
    {
        $this->authorizeOwnership($report);

        if (! in_array($report->status, [ReportStatus::Draft, ReportStatus::PendingReview, ReportStatus::Rejected])) {
            return redirect()->route('resident.reports.show', $report)
                ->with('error', 'This report can no longer be edited.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $report) {
            $oldValues = $report->only(['title', 'description', 'incident_date', 'location_description']);

            $report->update([
                'category_id' => $validated['category_id'],
                'barangay_id' => $validated['barangay_id'],
                'community_zone_id' => $validated['community_zone_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'incident_date' => $validated['incident_date'],
                'incident_time' => $validated['incident_time'] ?? null,
                'location_description' => $validated['location_description'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'contact_preference' => $validated['contact_preference'],
                'is_public' => $validated['is_public'] ?? true,
                'status' => ReportStatus::PendingReview,
            ]);

            $this->updateReportDetails($report, $validated);

            if ($request->hasFile('images')) {
                $this->handleImageUploads($report, $request->file('images'));
            }

            AuditLog::record('report_updated', $report, $oldValues, [
                'title' => $report->title,
            ]);
        });

        return redirect()->route('resident.reports.show', $report)
            ->with('success', 'Report updated successfully.');
    }

    public function destroyImage(Report $report, ReportImage $image): RedirectResponse
    {
        $this->authorizeOwnership($report);

        if ($image->report_id !== $report->id) {
            abort(403);
        }

        Storage::disk('public')->delete($image->file_path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function authorizeOwnership(Report $report): void
    {
        if ($report->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to access this report.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createReportDetails(Report $report, array $data): void
    {
        match ($report->type) {
            ReportType::LostProperty => LostPropertyDetail::create([
                'report_id' => $report->id,
                'brand' => $data['brand'] ?? null,
                'color' => $data['color'] ?? null,
                'model' => $data['model'] ?? null,
                'distinguishing_marks' => $data['distinguishing_marks'] ?? null,
                'serial_number_hint' => $data['serial_number_hint'] ?? null,
                'estimated_value' => $data['estimated_value'] ?? null,
            ]),
            ReportType::FoundProperty => FoundPropertyDetail::create([
                'report_id' => $report->id,
                'brand' => $data['brand'] ?? null,
                'color' => $data['color'] ?? null,
                'model' => $data['model'] ?? null,
                'distinguishing_marks' => $data['distinguishing_marks'] ?? null,
                'custody_status' => $data['custody_status'] ?? 'with_finder',
                'storage_location_hint' => $data['storage_location_hint'] ?? null,
            ]),
            ReportType::MissingPet => MissingPetDetail::create([
                'report_id' => $report->id,
                'pet_name' => $data['pet_name'] ?? null,
                'animal_type' => $data['animal_type'],
                'breed' => $data['breed'] ?? null,
                'color_markings' => $data['color_markings'] ?? null,
                'approximate_age' => $data['approximate_age'] ?? null,
                'size' => $data['size'] ?? null,
                'has_collar' => $data['has_collar'] ?? false,
                'collar_description' => $data['collar_description'] ?? null,
                'microchip_id_hint' => $data['microchip_id_hint'] ?? null,
                'medical_notes' => $data['medical_notes'] ?? null,
                'behavioral_notes' => $data['behavioral_notes'] ?? null,
                'reward_offered' => $data['reward_offered'] ?? false,
                'reward_description' => $data['reward_description'] ?? null,
            ]),
            ReportType::FoundPet => FoundPetDetail::create([
                'report_id' => $report->id,
                'animal_type' => $data['animal_type'],
                'breed_guess' => $data['breed_guess'] ?? null,
                'color_markings' => $data['color_markings'] ?? null,
                'approximate_age' => $data['approximate_age'] ?? null,
                'size' => $data['size'] ?? null,
                'has_collar' => $data['has_collar'] ?? false,
                'collar_description' => $data['collar_description'] ?? null,
                'visible_tag_info' => $data['visible_tag_info'] ?? null,
                'condition' => $data['condition'] ?? null,
                'immediate_needs' => $data['immediate_needs'] ?? null,
                'is_safely_sheltered' => $data['is_safely_sheltered'] ?? false,
                'shelter_location_hint' => $data['shelter_location_hint'] ?? null,
            ]),
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function updateReportDetails(Report $report, array $data): void
    {
        match ($report->type) {
            ReportType::LostProperty => $report->lostPropertyDetail?->update([
                'brand' => $data['brand'] ?? null,
                'color' => $data['color'] ?? null,
                'model' => $data['model'] ?? null,
                'distinguishing_marks' => $data['distinguishing_marks'] ?? null,
                'serial_number_hint' => $data['serial_number_hint'] ?? null,
                'estimated_value' => $data['estimated_value'] ?? null,
            ]),
            ReportType::FoundProperty => $report->foundPropertyDetail?->update([
                'brand' => $data['brand'] ?? null,
                'color' => $data['color'] ?? null,
                'model' => $data['model'] ?? null,
                'distinguishing_marks' => $data['distinguishing_marks'] ?? null,
                'custody_status' => $data['custody_status'] ?? 'with_finder',
                'storage_location_hint' => $data['storage_location_hint'] ?? null,
            ]),
            ReportType::MissingPet => $report->missingPetDetail?->update([
                'pet_name' => $data['pet_name'] ?? null,
                'animal_type' => $data['animal_type'],
                'breed' => $data['breed'] ?? null,
                'color_markings' => $data['color_markings'] ?? null,
                'approximate_age' => $data['approximate_age'] ?? null,
                'size' => $data['size'] ?? null,
                'has_collar' => $data['has_collar'] ?? false,
                'collar_description' => $data['collar_description'] ?? null,
                'microchip_id_hint' => $data['microchip_id_hint'] ?? null,
                'medical_notes' => $data['medical_notes'] ?? null,
                'behavioral_notes' => $data['behavioral_notes'] ?? null,
                'reward_offered' => $data['reward_offered'] ?? false,
                'reward_description' => $data['reward_description'] ?? null,
            ]),
            ReportType::FoundPet => $report->foundPetDetail?->update([
                'animal_type' => $data['animal_type'],
                'breed_guess' => $data['breed_guess'] ?? null,
                'color_markings' => $data['color_markings'] ?? null,
                'approximate_age' => $data['approximate_age'] ?? null,
                'size' => $data['size'] ?? null,
                'has_collar' => $data['has_collar'] ?? false,
                'collar_description' => $data['collar_description'] ?? null,
                'visible_tag_info' => $data['visible_tag_info'] ?? null,
                'condition' => $data['condition'] ?? null,
                'immediate_needs' => $data['immediate_needs'] ?? null,
                'is_safely_sheltered' => $data['is_safely_sheltered'] ?? false,
                'shelter_location_hint' => $data['shelter_location_hint'] ?? null,
            ]),
        };
    }

    /**
     * @param  array<UploadedFile>  $images
     */
    private function handleImageUploads(Report $report, array $images): void
    {
        $existingCount = $report->images()->count();
        $isPrimary = $existingCount === 0;

        foreach ($images as $index => $image) {
            if ($existingCount + $index >= 5) {
                break;
            }

            $path = $image->store("reports/{$report->id}", 'public');

            ReportImage::create([
                'report_id' => $report->id,
                'file_path' => $path,
                'file_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'file_size' => $image->getSize(),
                'is_primary' => $isPrimary && $index === 0,
                'sort_order' => $existingCount + $index,
            ]);
        }
    }
}
