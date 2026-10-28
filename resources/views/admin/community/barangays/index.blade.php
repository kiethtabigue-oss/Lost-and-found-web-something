@extends('layouts.admin')

@section('title', 'Community Barangays')
@section('page-title', 'Community Barangays')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Community Barangays</h4>
        <p class="text-muted small mb-0">Manage community jurisdictions, modify barangay names & codes, and oversee assigned zones & admins.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.barangays.create') }}" class="btn btn-primary shadow-sm text-nowrap">
            <i class="bi bi-plus-circle me-1"></i> Add Barangay
        </a>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-outline-primary shadow-sm text-nowrap">
            <i class="bi bi-person-plus me-1"></i> New Barangay Admin
        </a>
    </div>
</div>

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['total'] }}</div>
                    <div class="stat-label">Total Barangays</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['active'] }}</div>
                    <div class="stat-label">Active Jurisdictions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-signpost-2"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['total_zones'] }}</div>
                    <div class="stat-label">Community Zones</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['total_admins'] }}</div>
                    <div class="stat-label">Assigned Admins</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.barangays.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search barangay name, code, description..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill" title="Filter Barangays">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.barangays.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Barangays Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($barangays->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-2">No barangays found</h5>
            <p class="text-muted mb-3">No community barangays match your filter criteria.</p>
            <a href="{{ route('admin.barangays.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Create First Barangay
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 250px;">Barangay Name & Code</th>
                        <th>Description</th>
                        <th class="text-center" style="white-space: nowrap;">Zones</th>
                        <th class="text-center" style="white-space: nowrap;">Admins</th>
                        <th class="text-center" style="white-space: nowrap;">Residents</th>
                        <th class="text-center" style="white-space: nowrap;">Reports</th>
                        <th style="white-space: nowrap;">Coordinates</th>
                        <th style="white-space: nowrap;">Status</th>
                        <th class="text-end pe-3" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangays as $barangay)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.barangays.show', $barangay) }}" class="fw-bold text-dark text-decoration-none d-block hover-primary">
                                        {{ $barangay->name }}
                                    </a>
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                        {{ $barangay->code }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 240px;" title="{{ $barangay->description }}">
                                {{ $barangay->description ?: 'No description provided' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">
                                {{ $barangay->zones_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.accounts.index', ['role' => 'barangay_admin', 'barangay_id' => $barangay->id]) }}"
                               class="badge {{ $barangay->admins_count > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted' }} rounded-pill px-2 text-decoration-none">
                                {{ $barangay->admins_count }}
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-muted border rounded-pill px-2">
                                {{ $barangay->residents_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info rounded-pill px-2">
                                {{ $barangay->reports_count }}
                            </span>
                        </td>
                        <td>
                            @if($barangay->latitude && $barangay->longitude)
                            <span class="small font-monospace text-muted d-flex align-items-center gap-1" title="Lat: {{ $barangay->latitude }}, Long: {{ $barangay->longitude }}">
                                <i class="bi bi-pin-map text-danger"></i>
                                {{ number_format($barangay->latitude, 3) }}, {{ number_format($barangay->longitude, 3) }}
                            </span>
                            @else
                            <span class="text-muted small fst-italic">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($barangay->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                <i class="bi bi-pause-circle me-1"></i>Inactive
                            </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Manage
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.barangays.show', $barangay) }}">
                                            <i class="bi bi-eye text-primary me-2"></i>View Details & Zones
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.barangays.edit', $barangay) }}">
                                            <i class="bi bi-pencil-square text-info me-2"></i>Modify Name & Info
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.accounts.create', ['barangay_id' => $barangay->id]) }}">
                                            <i class="bi bi-person-plus text-success me-2"></i>Assign New Admin
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.barangays.toggle-status', $barangay) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item {{ $barangay->is_active ? 'text-warning' : 'text-success' }}">
                                                <i class="bi {{ $barangay->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                                                {{ $barangay->is_active ? 'Deactivate Barangay' : 'Activate Barangay' }}
                                            </button>
                                        </form>
                                    </li>
                                    @if($barangay->reports_count === 0 && $barangay->users_count === 0)
                                    <li>
                                        <form action="{{ route('admin.barangays.destroy', $barangay) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to permanently delete {{ $barangay->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash3 me-2"></i>Delete Barangay
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($barangays->hasPages())
        <div class="p-3 border-top">
            {{ $barangays->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
