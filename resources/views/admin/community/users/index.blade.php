@extends('layouts.admin')

@section('title', 'Barangay Accounts Management')
@section('page-title', 'Barangay & Community Accounts')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-person-badge-fill text-primary me-2"></i>Community Barangay Accounts</h4>
        <p class="text-muted small mb-0">Manage community officials, barangay administrators, resident accounts, and role permissions.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.accounts.create', ['role' => 'barangay_admin']) }}" class="btn btn-primary shadow-sm text-nowrap">
            <i class="bi bi-person-plus me-1"></i> Add Barangay Admin
        </a>
        <a href="{{ route('admin.accounts.create', ['role' => 'resident']) }}" class="btn btn-outline-primary shadow-sm text-nowrap">
            <i class="bi bi-person-add me-1"></i> Register Resident
        </a>
    </div>
</div>

{{-- Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['total_admins'] }}</div>
                    <div class="stat-label">Barangay Admins</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['active_admins'] }}</div>
                    <div class="stat-label">Active Moderators</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-person-slash"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['suspended_admins'] }}</div>
                    <div class="stat-label">Suspended Admins</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $metrics['total_residents'] }}</div>
                    <div class="stat-label">Registered Residents</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Role Tabs --}}
<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <a href="{{ route('admin.accounts.index', ['role' => 'barangay_admin'] + request()->except(['role', 'page'])) }}"
           class="nav-link px-4 py-2 {{ $currentRoleTab === 'barangay_admin' ? 'active' : '' }}">
            <i class="bi bi-shield-check me-1"></i> Barangay Administrators ({{ $metrics['total_admins'] }})
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.accounts.index', ['role' => 'resident'] + request()->except(['role', 'page'])) }}"
           class="nav-link px-4 py-2 {{ $currentRoleTab === 'resident' ? 'active' : '' }}">
            <i class="bi bi-people me-1"></i> Residents ({{ $metrics['total_residents'] }})
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.accounts.index', ['role' => 'all'] + request()->except(['role', 'page'])) }}"
           class="nav-link px-4 py-2 {{ $currentRoleTab === 'all' ? 'active' : '' }}">
            <i class="bi bi-list-check me-1"></i> All Accounts ({{ $metrics['total_all'] }})
        </a>
    </li>
</ul>

{{-- Filters Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.accounts.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="role" value="{{ $currentRoleTab }}">

            <div class="col-12 col-md-4 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search name, email, phone..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-3">
                <select name="barangay_id" class="form-select">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $b)
                    <option value="{{ $b->id }}" {{ request('barangay_id') == $b->id ? 'selected' : '' }}>
                        {{ $b->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                </select>
            </div>
            <div class="col-12 col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill" title="Filter Accounts">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'barangay_id', 'status']))
                <a href="{{ route('admin.accounts.index', ['role' => $currentRoleTab]) }}" class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Accounts Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($users->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-2">No accounts found</h5>
            <p class="text-muted mb-3">No user or admin accounts matched the selected filters.</p>
            <a href="{{ route('admin.accounts.create', ['role' => $currentRoleTab !== 'all' ? $currentRoleTab : 'barangay_admin']) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus me-1"></i> Create New Account
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 260px;">User & Email</th>
                        <th>Role</th>
                        <th>Assigned Barangay</th>
                        <th>Phone</th>
                        <th class="text-center">Reports</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end pe-3" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:36px;height:36px;font-size:0.8rem;font-weight:700;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <a href="{{ route('admin.accounts.show', $user) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">You</span>
                                        @endif
                                    </a>
                                    <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->isSuperAdmin())
                                <span class="badge bg-dark">Super Admin</span>
                            @elseif($user->isBarangayAdmin())
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <i class="bi bi-shield-check me-1"></i>Barangay Admin
                                </span>
                            @else
                                <span class="badge bg-light text-dark border">Resident</span>
                            @endif
                        </td>
                        <td>
                            @if($user->barangay)
                                <a href="{{ route('admin.barangays.show', $user->barangay) }}" class="text-decoration-none fw-medium text-dark d-flex align-items-center gap-1">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    {{ $user->barangay->name }}
                                </a>
                                <small class="text-muted font-monospace">{{ $user->barangay->code }}</small>
                            @else
                                <span class="text-muted small fst-italic">Global / None</span>
                            @endif
                        </td>
                        <td>
                            <span class="small">{{ $user->phone ?: '—' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-muted border rounded-pill px-2">
                                {{ $user->reports_count }}
                            </span>
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                            @elseif($user->status === 'suspended')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Suspended</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border">{{ ucfirst($user->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                        </td>
                        <td class="text-end pe-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.accounts.show', $user) }}">
                                            <i class="bi bi-eye text-primary me-2"></i>View Account Details
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.accounts.edit', $user) }}">
                                            <i class="bi bi-pencil text-info me-2"></i>Edit Information
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}">
                                            <i class="bi bi-key text-warning me-2"></i>Reset Password
                                        </button>
                                    </li>
                                    @if($user->id !== auth()->id())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.accounts.toggle-status', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item {{ $user->status === 'active' ? 'text-danger' : 'text-success' }}">
                                                <i class="bi {{ $user->status === 'active' ? 'bi-lock' : 'bi-unlock' }} me-2"></i>
                                                {{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.accounts.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete account {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash3 me-2"></i>Delete Account
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            {{-- Reset Password Modal for This User --}}
                            <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog text-start">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.accounts.reset-password', $user) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Reset Password: {{ $user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small">Set a new temporary password for <strong>{{ $user->email }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">New Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="new_password" class="form-control" placeholder="Min. 8 characters" required minlength="8">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Confirm New Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="new_password_confirmation" class="form-control" placeholder="Repeat new password" required minlength="8">
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
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-3 border-top">
            {{ $users->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
