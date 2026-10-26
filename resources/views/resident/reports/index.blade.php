@extends('layouts.resident')

@section('title', 'My Reports')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-primary me-2"></i>My Reports</h3>
        <p class="text-muted mb-0">Track and manage your submitted lost and found reports.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <div class="dropdown d-inline-block">
            <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-plus-circle me-1"></i> Submit New Report
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
</div>

{{-- Filters Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('resident.reports.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search title, reference #..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(\App\Enums\ReportType::cases() as $typeCase)
                    <option value="{{ $typeCase->value }}" {{ request('type') === $typeCase->value ? 'selected' : '' }}>
                        {{ $typeCase->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(\App\Enums\ReportStatus::cases() as $statusCase)
                    <option value="{{ $statusCase->value }}" {{ request('status') === $statusCase->value ? 'selected' : '' }}>
                        {{ $statusCase->label() }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'type', 'status']))
                <a href="{{ route('resident.reports.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
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
            <div class="mb-3">
                <i class="bi bi-clipboard2-x text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5 class="fw-bold">No reports found</h5>
            <p class="text-muted mb-3">You haven't submitted any reports matching your search or filters.</p>
            <a href="{{ route('resident.reports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Create Your First Report
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Item</th>
                        <th>Reference & Title</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Barangay / Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
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
                            <div class="fw-bold text-dark mb-0">{{ $report->title }}</div>
                            <span class="badge bg-light text-secondary border font-monospace">{{ $report->reference_number }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $report->type->badgeClass() }}">
                                <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $report->category->name ?? '—' }}</span>
                        </td>
                        <td>
                            <div class="small fw-medium">{{ $report->barangay->name ?? '—' }}</div>
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $report->incident_date->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $report->status->badgeClass() }}">
                                {{ $report->status->label() }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('resident.reports.show', $report) }}" class="btn btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if(in_array($report->status->value, ['draft', 'pending_review', 'rejected']))
                                <a href="{{ route('resident.reports.edit', $report) }}" class="btn btn-outline-secondary" title="Edit Report">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
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
