@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stats Row 1: Report Type Totals --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_lost_property'] }}</div>
                    <div class="stat-label">Lost Property</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_found_property'] }}</div>
                    <div class="stat-label">Found Property</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-search-heart"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_missing_pets'] }}</div>
                    <div class="stat-label">Missing Pets</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-heart"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_found_pets'] }}</div>
                    <div class="stat-label">Found Pets</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats Row 2: System Metrics --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Registered Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['pending_reports'] }}</div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['recovered_reports'] }}</div>
                    <div class="stat-label">Recovered / Reunited</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['recovery_rate'] }}%</div>
                    <div class="stat-label">Recovery Rate</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Reports Table --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Reports</span>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentReports->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No reports submitted yet.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Reference</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Submitted By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $report)
                            <tr>
                                <td><a href="{{ route('admin.reports.show', $report) }}" class="badge bg-light text-secondary border font-monospace text-decoration-none">{{ $report->reference_number }}</a></td>
                                <td>
                                    <a href="{{ route('admin.reports.show', $report) }}" class="fw-semibold text-dark text-decoration-none">
                                        {{ Str::limit($report->title, 32) }}
                                    </a>
                                </td>
                                <td><span class="badge {{ $report->type->badgeClass() }}">{{ $report->type->label() }}</span></td>
                                <td><span class="badge {{ $report->status->badgeClass() }}">{{ $report->status->label() }}</span></td>
                                <td>{{ $report->user->name ?? 'N/A' }}</td>
                                <td class="small text-muted">{{ $report->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Pending Claims --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-check me-2"></i>Pending Claims</span>
                <span class="badge bg-warning text-dark rounded-pill">{{ $stats['pending_claims'] }}</span>
            </div>
            <div class="card-body p-0">
                @if($recentClaims->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-file-earmark"></i>
                    <p class="small">No claims submitted yet.</p>
                </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($recentClaims as $claim)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-medium small">{{ $claim->claimant->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ Str::limit($claim->report->title ?? '', 35) }}</div>
                            </div>
                            <span class="badge {{ $claim->status->badgeClass() }} small">{{ $claim->status->label() }}</span>
                        </div>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-clock me-1"></i>{{ $claim->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
