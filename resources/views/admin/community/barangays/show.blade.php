@extends('layouts.admin')

@section('title', $barangay->name . ' — Details')
@section('page-title', $barangay->name)

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width:54px;height:54px;">
            <i class="bi bi-building fs-3"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0">{{ $barangay->name }}</h4>
                <span class="badge bg-light text-secondary border font-monospace">{{ $barangay->code }}</span>
                @if($barangay->is_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
                @endif
            </div>
            <p class="text-muted small mb-0">{{ $barangay->description ?: 'No description provided.' }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.barangays.edit', $barangay) }}" class="btn btn-outline-primary text-nowrap">
            <i class="bi bi-pencil-square me-1"></i> Edit Barangay Name
        </a>
        <a href="{{ route('admin.accounts.create', ['barangay_id' => $barangay->id]) }}" class="btn btn-primary text-nowrap">
            <i class="bi bi-person-plus me-1"></i> Add Admin
        </a>
        <form action="{{ route('admin.barangays.toggle-status', $barangay) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-light border text-nowrap" title="{{ $barangay->is_active ? 'Deactivate' : 'Activate' }}">
                <i class="bi {{ $barangay->is_active ? 'bi-pause-circle text-warning' : 'bi-play-circle text-success' }}"></i>
            </button>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_admins'] }}</div>
                    <div class="stat-label">Barangay Admins</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-signpost-2"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_zones'] }}</div>
                    <div class="stat-label">Community Zones</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_reports'] }}</div>
                    <div class="stat-label">Total Reports</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_residents'] }}</div>
                    <div class="stat-label">Residents</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs Navigation --}}
<ul class="nav nav-pills mb-4 gap-2" id="barangayTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active px-4 py-2" id="zones-tab" data-bs-toggle="pill" data-bs-target="#zones-pane" type="button" role="tab">
            <i class="bi bi-signpost-2 me-1"></i> Community Zones ({{ $zones->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link px-4 py-2" id="admins-tab" data-bs-toggle="pill" data-bs-target="#admins-pane" type="button" role="tab">
            <i class="bi bi-shield-check me-1"></i> Barangay Admins ({{ $admins->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link px-4 py-2" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports-pane" type="button" role="tab">
            <i class="bi bi-file-earmark-text me-1"></i> Recent Reports ({{ $recentReports->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link px-4 py-2" id="info-tab" data-bs-toggle="pill" data-bs-target="#info-pane" type="button" role="tab">
            <i class="bi bi-info-circle me-1"></i> Jurisdiction Info
        </button>
    </li>
</ul>

{{-- Tab Contents --}}
<div class="tab-content" id="barangayTabsContent">
    {{-- TAB 1: Community Zones --}}
    <div class="tab-pane fade show active" id="zones-pane" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-signpost-2 text-primary me-2"></i>Community Zones in {{ $barangay->name }}</h6>
                    <small class="text-muted">Zones allow residents to pinpoint exact sub-locations when filing reports.</small>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Zone
                </button>
            </div>
            <div class="card-body p-0">
                @if($zones->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-signpost-split text-muted" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold mt-2">No community zones created yet</h6>
                    <p class="text-muted small mb-3">Add zones like "Zone 1 - Residential", "Commercial Plaza", or "North Sector".</p>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                        <i class="bi bi-plus-circle me-1"></i> Create First Zone
                    </button>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Zone Name</th>
                                <th>Description</th>
                                <th style="white-space: nowrap;">Status</th>
                                <th class="text-end pe-3" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $zone)
                            <tr>
                                <td class="ps-3 fw-medium">
                                    <i class="bi bi-geo text-muted me-1"></i> {{ $zone->name }}
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $zone->description ?: '—' }}</span>
                                </td>
                                <td>
                                    @if($zone->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal" data-bs-target="#editZoneModal{{ $zone->id }}" title="Edit Zone">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.zones.toggle-status', $zone) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="{{ $zone->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi {{ $zone->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.zones.destroy', $zone) }}" method="POST"
                                              onsubmit="return confirm('Delete zone {{ $zone->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Zone">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Modal for Editing Zone --}}
                                    <div class="modal fade" id="editZoneModal{{ $zone->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog text-start">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.zones.update', $zone) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit Zone: {{ $zone->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-medium">Zone Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $zone->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-medium">Description</label>
                                                            <textarea name="description" class="form-control" rows="2">{{ $zone->description }}</textarea>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="zoneActive{{ $zone->id }}" {{ $zone->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-medium" for="zoneActive{{ $zone->id }}">Active Zone</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TAB 2: Barangay Admins --}}
    <div class="tab-pane fade" id="admins-pane" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check text-primary me-2"></i>Assigned Barangay Administrators</h6>
                    <small class="text-muted">Staff and barangay officials with moderation rights for {{ $barangay->name }}.</small>
                </div>
                <a href="{{ route('admin.accounts.create', ['barangay_id' => $barangay->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Add Barangay Admin
                </a>
            </div>
            <div class="card-body p-0">
                @if($admins->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-person-x text-muted" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold mt-2">No Barangay Admins assigned</h6>
                    <p class="text-muted small mb-3">Assign a moderator account to manage incoming reports for this barangay.</p>
                    <a href="{{ route('admin.accounts.create', ['barangay_id' => $barangay->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Create Admin Account
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Name & Email</th>
                                <th>Phone</th>
                                <th style="white-space: nowrap;">Status</th>
                                <th style="white-space: nowrap;">Joined</th>
                                <th class="text-end pe-3" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $adminUser)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width:34px;height:34px;font-size:0.75rem;font-weight:700;">
                                            {{ strtoupper(substr($adminUser->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.accounts.show', $adminUser) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                {{ $adminUser->name }}
                                            </a>
                                            <small class="text-muted">{{ $adminUser->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $adminUser->phone ?: '—' }}</td>
                                <td>
                                    @if($adminUser->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                    @elseif($adminUser->status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary border">{{ ucfirst($adminUser->status) }}</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $adminUser->created_at->format('M d, Y') }}</small></td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.accounts.edit', $adminUser) }}" class="btn btn-sm btn-outline-secondary" title="Edit Admin">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.accounts.toggle-status', $adminUser) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $adminUser->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $adminUser->status === 'active' ? 'Suspend Account' : 'Activate Account' }}">
                                                <i class="bi {{ $adminUser->status === 'active' ? 'bi-lock' : 'bi-unlock' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TAB 3: Recent Reports --}}
    <div class="tab-pane fade" id="reports-pane" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text text-primary me-2"></i>Reports in {{ $barangay->name }}</h6>
                <a href="{{ route('admin.reports.index', ['barangay_id' => $barangay->id]) }}" class="btn btn-sm btn-outline-primary">
                    View All Barangay Reports <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentReports->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold mt-2">No reports recorded for this barangay</h6>
                    <p class="text-muted small mb-0">Reports filed by residents in this jurisdiction will appear here.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Reference & Title</th>
                                <th>Type</th>
                                <th>Reporter</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $report)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $report->title }}
                                    </a>
                                    <div class="small font-monospace text-muted">{{ $report->reference_number }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $report->type->label() }}</span>
                                </td>
                                <td>{{ $report->user->name }}</td>
                                <td><small class="text-muted">{{ $report->incident_date?->format('M d, Y') ?: '—' }}</small></td>
                                <td>
                                    <span class="badge {{ $report->status->badgeClass() }}">{{ $report->status->label() }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
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
    </div>

    {{-- TAB 4: Jurisdiction Info --}}
    <div class="tab-pane fade" id="info-pane" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle text-primary me-2"></i>Technical & Coordinate Details</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 35%;">Barangay Name</th>
                                    <td>{{ $barangay->name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">System Code</th>
                                    <td class="font-monospace fw-bold text-primary">{{ $barangay->code }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Status</th>
                                    <td>
                                        @if($barangay->is_active)
                                            <span class="badge bg-success-subtle text-success">Active Jurisdiction</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Latitude</th>
                                    <td class="font-monospace">{{ $barangay->latitude ?: 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Longitude</th>
                                    <td class="font-monospace">{{ $barangay->longitude ?: 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Created At</th>
                                    <td>{{ $barangay->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Last Updated</th>
                                    <td>{{ $barangay->updated_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <h6 class="fw-semibold mb-2"><i class="bi bi-pin-map text-danger me-1"></i>Geographic Coverage</h6>
                            <p class="text-muted small mb-3">
                                Coordinates provide the default focal center for map search and lost/found radius matching in {{ $barangay->name }}.
                            </p>
                            @if($barangay->latitude && $barangay->longitude)
                            <div class="alert alert-secondary mb-0 small">
                                <strong>Center Location:</strong> Lat {{ $barangay->latitude }}, Long {{ $barangay->longitude }}
                                <div class="mt-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $barangay->latitude }},{{ $barangay->longitude }}"
                                       target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Open in Google Maps
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-warning mb-0 small">
                                <i class="bi bi-exclamation-triangle me-1"></i> Coordinates are not set yet.
                                <a href="{{ route('admin.barangays.edit', $barangay) }}" class="alert-link">Edit barangay to set coordinates.</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Adding Zone --}}
<div class="modal fade" id="addZoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.barangays.zones.store', $barangay) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Zone in {{ $barangay->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Zone Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Zone 4 - Riverside" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Brief landmark or boundary description"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newZoneActive" checked>
                        <label class="form-check-label fw-medium" for="newZoneActive">Active Zone immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Zone</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
