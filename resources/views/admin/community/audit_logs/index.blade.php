@extends('layouts.admin')

@section('title', 'System & Community Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history text-primary me-2"></i>System & Community Audit Logs</h4>
        <p class="text-muted small mb-0">Track administrative operations, barangay modifications, account updates, and authentication trails.</p>
    </div>
</div>

{{-- Filters Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4 col-xl-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search action or IP..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', ucfirst($act)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <select name="user_id" class="form-select">
                    <option value="">All Admins</option>
                    @foreach($admins as $adminUser)
                    <option value="{{ $adminUser->id }}" {{ request('user_id') == $adminUser->id ? 'selected' : '' }}>
                        {{ $adminUser->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}" title="Date from">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}" title="Date to">
            </div>
            <div class="col-12 col-md-6 col-xl-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill" title="Filter Logs">
                    <i class="bi bi-funnel"></i>
                </button>
                @if(request()->hasAny(['search', 'action', 'user_id', 'date_from', 'date_to', 'auditable_type']))
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Audit Logs Table Card --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($logs->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-2">No audit logs found</h5>
            <p class="text-muted mb-0">No system activities recorded for the selected search criteria.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 170px;">Timestamp</th>
                        <th>User / Admin</th>
                        <th>Action</th>
                        <th>Target Model</th>
                        <th>Details & Changes</th>
                        <th class="text-end pe-3">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="ps-3 font-monospace small text-muted">
                            <div>{{ $log->created_at->format('Y-m-d H:i') }}</div>
                            <span class="text-muted small" style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if($log->user)
                                <a href="{{ route('admin.accounts.show', $log->user) }}" class="fw-medium text-dark text-decoration-none d-block">
                                    {{ $log->user->name }}
                                </a>
                                <small class="text-muted">{{ $log->user->role?->name ?: 'User' }}</small>
                            @else
                                <span class="text-muted small fst-italic">System / Guest</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match(true) {
                                    str_contains($log->action, 'create') => 'bg-success-subtle text-success border border-success-subtle',
                                    str_contains($log->action, 'update') => 'bg-info-subtle text-info border border-info-subtle',
                                    str_contains($log->action, 'delete') => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    str_contains($log->action, 'toggle') || str_contains($log->action, 'status') => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    default => 'bg-light text-dark border',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} font-monospace" style="font-size: 0.75rem;">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            @if($log->auditable_type)
                                <span class="small font-monospace text-secondary">
                                    {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log->new_values || $log->old_values)
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0"
                                        data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}">
                                    <i class="bi bi-code-slash me-1"></i>View Diff
                                </button>
                                <div class="collapse mt-2" id="logDetails{{ $log->id }}">
                                    <div class="bg-light p-2 rounded small font-monospace" style="font-size: 0.75rem; max-width: 450px; overflow-x: auto;">
                                        @if($log->old_values)
                                            <div class="text-danger"><strong>- Old:</strong> {{ json_encode($log->old_values, JSON_UNESCAPED_SLASHES) }}</div>
                                        @endif
                                        @if($log->new_values)
                                            <div class="text-success"><strong>+ New:</strong> {{ json_encode($log->new_values, JSON_UNESCAPED_SLASHES) }}</div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">No payload logged</span>
                            @endif
                        </td>
                        <td class="text-end pe-3 font-monospace small text-muted">
                            {{ $log->ip_address ?: '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-3 border-top">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
