@extends('layouts.admin')

@section('title', 'Pending Moderation Queue')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clipboard-check text-warning me-2"></i>Moderation Queue</h4>
        <p class="text-muted mb-0">Review pending reports submitted by residents before they are publicly listed.</p>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> All Reports
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
            Pending Reports ({{ $reports->total() }})
        </h6>
        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Awaiting Action</span>
    </div>
    <div class="card-body p-0">
        @if($reports->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-check2-all text-success" style="font-size: 3.5rem;"></i>
            <h5 class="fw-bold mt-2">All Caught Up!</h5>
            <p class="text-muted mb-0">There are no reports currently waiting for review.</p>
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
                        <th class="text-end pe-3" style="width: 220px; white-space: nowrap;">Moderation Actions</th>
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
                            <a href="{{ route('admin.reports.show', $report) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 240px;">
                                {{ $report->title }}
                            </a>
                            <div class="small d-flex flex-wrap align-items-center gap-1 mt-1">
                                <span class="badge bg-light text-secondary border font-monospace">{{ $report->reference_number }}</span>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 180px;">{{ $report->description }}</span>
                            </div>
                        </td>
                        <td style="white-space: nowrap;">
                            <span class="badge {{ $report->type->badgeClass() }}">
                                <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-medium small text-dark text-truncate" style="max-width: 160px;">{{ $report->user->name ?? 'Unknown' }}</div>
                            <small class="text-muted text-truncate d-block" style="max-width: 160px;">{{ $report->user->email ?? '' }}</small>
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="small fw-medium">{{ $report->barangay->name ?? '—' }}</div>
                            <small class="text-muted">{{ $report->incident_date->format('M d, Y') }}</small>
                        </td>
                        <td class="text-end pe-3" style="white-space: nowrap;">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-outline-secondary" title="View Full Details">
                                    <i class="bi bi-eye"></i> Details
                                </a>

                                <form method="POST" action="{{ route('admin.reports.approve', $report) }}" class="d-inline"
                                      onsubmit="return confirm('Approve report {{ $report->reference_number }} and make it active?');">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Approve Report">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger reject-btn"
                                        data-url="{{ route('admin.reports.reject', $report) }}"
                                        data-reference="{{ $report->reference_number }}"
                                        title="Reject Report">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
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

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Reject Report <span id="rejectModalRef" class="font-monospace"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Please provide a reason for rejecting this report. This will be visible to the submitting resident.</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-medium">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"
                                  placeholder="e.g., Incomplete information, unclear photo, inappropriate content..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.reject-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = document.getElementById('rejectForm');
        form.action = this.dataset.url;
        document.getElementById('rejectModalRef').textContent = this.dataset.reference;
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    });
});
</script>
@endpush
