@extends('layouts.resident')

@section('title', 'My Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h3 class="fw-bold mb-1">Welcome back, <span class="text-primary">{{ auth()->user()->name }}</span>!</h3>
        <p class="text-muted mb-0">Here is a real-time summary of your community recovery reports and alerts.</p>
    </div>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-plus-circle me-1"></i> New Report
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item py-2" href="{{ route('resident.reports.create', ['type' => 'lost_property']) }}"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Lost Property</a></li>
            <li><a class="dropdown-item py-2" href="{{ route('resident.reports.create', ['type' => 'found_property']) }}"><i class="bi bi-box-seam text-success me-2"></i>Found Property</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item py-2" href="{{ route('resident.reports.create', ['type' => 'missing_pet']) }}"><i class="bi bi-search-heart text-warning me-2"></i>Missing Pet</a></li>
            <li><a class="dropdown-item py-2" href="{{ route('resident.reports.create', ['type' => 'found_pet']) }}"><i class="bi bi-heart text-info me-2"></i>Found Pet</a></li>
        </ul>
    </div>
</div>

{{-- Stats Row 1 --}}
<div class="row g-3 mb-3">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_reports'] }}</div>
                    <div class="stat-label">Total Reports</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['active_reports'] }}</div>
                    <div class="stat-label">Active Listings</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['recovered_reports'] }}</div>
                    <div class="stat-label">Recovered & Resolved</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats Row 2 --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['pending_claims'] }}</div>
                    <div class="stat-label">Pending Claims</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-purple bg-opacity-10" style="color:#7c3aed;background:rgba(124,58,237,0.1);">
                    <i class="bi bi-magic"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['possible_matches'] }}</div>
                    <div class="stat-label">Potential Matches</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-bell"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['unread_notifications'] }}</div>
                    <div class="stat-label">Unread Notifications</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h6>
    </div>
    <div class="card-body p-3 pt-0">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <a href="{{ route('resident.reports.create', ['type' => 'lost_property']) }}"
                   class="btn btn-outline-danger w-100 py-3 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm">
                    <i class="bi bi-exclamation-triangle fs-3 mb-1"></i>
                    <span class="fw-semibold">Report Lost Item</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('resident.reports.create', ['type' => 'found_property']) }}"
                   class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm">
                    <i class="bi bi-box-seam fs-3 mb-1"></i>
                    <span class="fw-semibold">Report Found Item</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('resident.reports.create', ['type' => 'missing_pet']) }}"
                   class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm">
                    <i class="bi bi-search-heart fs-3 mb-1"></i>
                    <span class="fw-semibold">Report Missing Pet</span>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ route('resident.reports.create', ['type' => 'found_pet']) }}"
                   class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center justify-content-center text-decoration-none shadow-sm">
                    <i class="bi bi-heart fs-3 mb-1"></i>
                    <span class="fw-semibold">Report Found Pet</span>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- My Recent Reports Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>My Recent Reports</h6>
        <a href="{{ route('resident.reports.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3">
            View All Reports <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        @if($myReports->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-file-earmark-plus text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-2">No reports yet</h5>
            <p class="text-muted mb-3">You haven't submitted any lost or found reports yet.</p>
            <a href="{{ route('resident.reports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Create Your First Report
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Incident Date</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myReports as $report)
                    <tr>
                        <td>
                            <a href="{{ route('resident.reports.show', $report) }}" class="badge bg-light text-secondary border font-monospace text-decoration-none">
                                {{ $report->reference_number }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('resident.reports.show', $report) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ Str::limit($report->title, 40) }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $report->type->badgeClass() }}">
                                <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $report->status->badgeClass() }}">
                                {{ $report->status->label() }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $report->incident_date->format('M d, Y') }}
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('resident.reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
