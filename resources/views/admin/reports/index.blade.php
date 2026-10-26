@extends('layouts.admin')

@section('title', 'Report Management')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-primary me-2"></i>Report Management</h4>
        <p class="text-muted small mb-0">Browse, filter, and moderate community lost and found reports.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.pending') }}" class="btn btn-warning shadow-sm text-nowrap">
            <i class="bi bi-clipboard-check me-1"></i> Moderation Queue
        </a>
    </div>
</div>

{{-- Filters Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search title, reference..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(\App\Enums\ReportType::cases() as $typeCase)
                    <option value="{{ $typeCase->value }}" {{ request('type') === $typeCase->value ? 'selected' : '' }}>
                        {{ $typeCase->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(\App\Enums\ReportStatus::cases() as $statusCase)
                    <option value="{{ $statusCase->value }}" {{ request('status') === $statusCase->value ? 'selected' : '' }}>
                        {{ $statusCase->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}" title="Incident date from">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}" title="Incident date to">
            </div>
            <div class="col-12 col-md-6 col-xl-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill" title="Apply Filters">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to', 'barangay_id']))
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Reports Table Card --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($reports->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-2">No reports found</h5>
            <p class="text-muted mb-0">No reports matched your search criteria.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 56px;" class="ps-3">Item</th>
                        <th>Reference & Title</th>
                        <th style="white-space: nowrap;">Type</th>
                        <th>Reporter</th>
                        <th style="white-space: nowrap;">Barangay / Date</th>
                        <th style="white-space: nowrap;">Status</th>
                        <th class="text-end pe-3" style="width: 90px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr>
                        <td class="ps-3">
                            @php
                                $firstImg = $report->images->first();
                            @endphp
                            @if($firstImg && $firstImg->file_path)
                                <img src="{{ asset('storage/' . ltrim($firstImg->file_path, '/')) }}"
                                     alt="{{ $report->title }}"
                                     class="rounded object-fit-cover border shadow-xs"
                                     width="44" height="44"
                                     onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'bg-light rounded d-flex align-items-center justify-content-center text-secondary border\' style=\'width:44px;height:44px;\'><i class=\'bi {{ $report->type->icon() }} fs-5\'></i></div>';">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-secondary border" style="width: 44px; height: 44px;">
                                    <i class="bi {{ $report->type->icon() }} fs-5"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.reports.show', $report) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 250px;">
                                {{ $report->title }}
                            </a>
                            <div class="small d-flex flex-wrap align-items-center gap-1 mt-1">
                                <span class="badge bg-light text-secondary border font-monospace">{{ $report->reference_number }}</span>
                                @if($report->category)
                                <span class="text-muted small">{{ $report->category->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td style="white-space: nowrap;">
                            <span class="badge {{ $report->type->badgeClass() }}">
                                <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-medium small text-dark text-truncate" style="max-width: 170px;">{{ $report->user->name ?? 'Unknown' }}</div>
                            <small class="text-muted text-truncate d-block" style="max-width: 170px;">{{ $report->user->email ?? '' }}</small>
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="small fw-medium">{{ $report->barangay->name ?? '—' }}</div>
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $report->incident_date->format('M d, Y') }}</small>
                        </td>
                        <td style="white-space: nowrap;">
                            <span class="badge {{ $report->status->badgeClass() }}">
                                {{ $report->status->label() }}
                            </span>
                        </td>
                        <td class="text-end pe-3" style="white-space: nowrap;">
                            <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $reports->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
