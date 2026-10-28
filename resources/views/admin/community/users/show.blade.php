@extends('layouts.admin')

@section('title', 'Account Profile — ' . $account->name)
@section('page-title', 'Account Profile')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center"
             style="width:56px;height:56px;font-size:1.25rem;font-weight:700;">
            {{ strtoupper(substr($account->name, 0, 2)) }}
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0">{{ $account->name }}</h4>
                @if($account->status === 'active')
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                @elseif($account->status === 'suspended')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border">{{ ucfirst($account->status) }}</span>
                @endif

                @if($account->isSuperAdmin())
                    <span class="badge bg-dark">Super Admin</span>
                @elseif($account->isBarangayAdmin())
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Barangay Admin</span>
                @else
                    <span class="badge bg-light text-dark border">Resident</span>
                @endif
            </div>
            <p class="text-muted small mb-0">{{ $account->email }} &bull; {{ $account->phone ?: 'No phone provided' }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.accounts.edit', $account) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit Account
        </a>
        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
            <i class="bi bi-key me-1"></i> Reset Password
        </button>
        @if($account->id !== auth()->id())
        <form action="{{ route('admin.accounts.toggle-status', $account) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn {{ $account->status === 'active' ? 'btn-light border text-danger' : 'btn-light border text-success' }}">
                <i class="bi {{ $account->status === 'active' ? 'bi-lock' : 'bi-unlock' }}"></i>
            </button>
        </form>
        @endif
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Left column: Account & Barangay Details --}}
    <div class="col-lg-4">
        {{-- Profile Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person text-primary me-2"></i>Profile Information</h6>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 40%;">Full Name</td>
                            <td class="fw-medium">{{ $account->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td class="fw-medium text-break">{{ $account->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td>{{ $account->phone ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Role</td>
                            <td>{{ $account->role->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($account->status === 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @elseif($account->status === 'suspended')
                                    <span class="badge bg-danger-subtle text-danger">Suspended</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($account->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Verified</td>
                            <td>
                                @if($account->email_verified_at)
                                    <span class="text-success small"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                @else
                                    <span class="text-muted small">Unverified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Registered</td>
                            <td>{{ $account->created_at->format('M d, Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Assigned Barangay Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-geo-alt text-primary me-2"></i>Assigned Barangay</h6>
            </div>
            <div class="card-body p-3">
                @if($account->barangay)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <a href="{{ route('admin.barangays.show', $account->barangay) }}" class="fw-bold text-dark text-decoration-none d-block">
                            {{ $account->barangay->name }}
                        </a>
                        <small class="text-muted font-monospace">{{ $account->barangay->code }}</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">{{ $account->barangay->description ?: 'No description' }}</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.barangays.show', $account->barangay) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-eye me-1"></i> View Barangay
                    </a>
                    <a href="{{ route('admin.barangays.edit', $account->barangay) }}" class="btn btn-sm btn-light border">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-geo text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted small mb-2">Not assigned to any specific barangay.</p>
                    <a href="{{ route('admin.accounts.edit', $account) }}" class="btn btn-sm btn-outline-primary">
                        Assign Barangay
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right column: Activity, Reports & Logs --}}
    <div class="col-lg-8">
        {{-- Reports Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text text-primary me-2"></i>Reports by {{ $account->name }} ({{ $account->reports_count }})</h6>
            </div>
            <div class="card-body p-0">
                @if($recentReports->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted small mb-0">No reports submitted by this user account.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Reference & Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $rep)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.reports.show', $rep) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $rep->title }}
                                    </a>
                                    <div class="small font-monospace text-muted">{{ $rep->reference_number }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $rep->category?->name ?: '—' }}</span></td>
                                <td><span class="badge {{ $rep->status->badgeClass() }}">{{ $rep->status->label() }}</span></td>
                                <td><small class="text-muted">{{ $rep->incident_date?->format('M d, Y') ?: '—' }}</small></td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.reports.show', $rep) }}" class="btn btn-sm btn-outline-primary">
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

        {{-- Audit Trail Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history text-primary me-2"></i>Recent Account Activity & Audit History</h6>
            </div>
            <div class="card-body p-0">
                @if($auditLogs->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted small mb-0">No audit log entries recorded for this account.</p>
                </div>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($auditLogs as $log)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div>
                            <span class="badge bg-light text-dark border font-monospace me-2">{{ $log->action }}</span>
                            <span class="small text-muted">by {{ $log->user?->name ?: 'System' }}</span>
                            @if($log->ip_address)
                            <span class="small text-muted font-monospace ms-2">({{ $log->ip_address }})</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.accounts.reset-password', $account) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Enter a new secure password for <strong>{{ $account->name }}</strong>.</p>
                    <div class="mb-3">
                        <label class="form-label fw-medium">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" placeholder="Min. 8 characters" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repeat password" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
