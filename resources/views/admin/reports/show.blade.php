@extends('layouts.admin')

@section('title', 'Admin Review — ' . $report->reference_number)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">{{ $report->reference_number }}</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    {{-- Main Content Column --}}
    <div class="col-lg-8">
        {{-- Report Overview Header --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge {{ $report->type->badgeClass() }} py-2 px-3 fs-6">
                                <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
                            </span>
                            <span class="badge {{ $report->status->badgeClass() }} py-2 px-3 fs-6">
                                {{ $report->status->label() }}
                            </span>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $report->title }}</h2>
                        <div class="text-muted small">
                            <span class="font-monospace fw-bold me-3"><i class="bi bi-hash"></i>{{ $report->reference_number }}</span>
                            <span class="me-3"><i class="bi bi-calendar-event me-1"></i>Incident: {{ $report->incident_date->format('M d, Y') }}</span>
                            <span><i class="bi bi-clock me-1"></i>Submitted: {{ $report->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <hr class="my-3 text-muted">

                <h6 class="fw-bold text-uppercase text-secondary small mb-2">Item / Incident Description</h6>
                <p class="text-secondary leading-relaxed mb-0" style="white-space: pre-line;">{{ $report->description }}</p>
            </div>
        </div>

        {{-- Photos --}}
        @if($report->images->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0"><i class="bi bi-images text-primary me-2"></i>Uploaded Evidence / Photos ({{ $report->images->count() }})</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    @foreach($report->images as $image)
                    <div class="col-6 col-md-4">
                        <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank" class="d-block text-decoration-none">
                            <img src="{{ asset('storage/' . $image->file_path) }}"
                                 alt="Report photo"
                                 class="img-fluid rounded shadow-sm object-fit-cover w-100"
                                 style="height: 180px;">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Type-Specific Details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0"><i class="bi bi-card-checklist text-primary me-2"></i>Detailed Attributes</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($report->type->value === 'lost_property' && $report->lostPropertyDetail)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Brand</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->brand ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->color ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Model</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->model ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Estimated Value</small>
                        <span class="fw-semibold text-dark">
                            {{ $report->lostPropertyDetail->estimated_value ? '₱' . number_format($report->lostPropertyDetail->estimated_value, 2) : '—' }}
                        </span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Distinguishing Marks</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->distinguishing_marks ?? 'None listed' }}</span>
                    </div>
                    @if($report->lostPropertyDetail->serial_number_hint)
                    <div class="col-12">
                        <small class="text-muted d-block">Serial Number Hint</small>
                        <span class="font-monospace bg-light p-1 rounded">{{ $report->lostPropertyDetail->serial_number_hint }}</span>
                    </div>
                    @endif
                </div>

                @elseif($report->type->value === 'found_property' && $report->foundPropertyDetail)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Brand</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->brand ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->color ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Model</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->model ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Custody Status</small>
                        <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $report->foundPropertyDetail->custody_status)) }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Distinguishing Marks</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->distinguishing_marks ?? 'None listed' }}</span>
                    </div>
                    @if($report->foundPropertyDetail->storage_location_hint)
                    <div class="col-12">
                        <small class="text-muted d-block">Storage Location Hint</small>
                        <span class="text-dark">{{ $report->foundPropertyDetail->storage_location_hint }}</span>
                    </div>
                    @endif
                </div>

                @elseif($report->type->value === 'missing_pet' && $report->missingPetDetail)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Pet Name</small>
                        <span class="fw-bold fs-5 text-dark">{{ $report->missingPetDetail->pet_name ?? 'Unnamed' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Animal Type / Breed</small>
                        <span class="fw-semibold text-capitalize text-dark">{{ $report->missingPetDetail->animal_type }} &bull; {{ $report->missingPetDetail->breed ?? 'Mixed' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color & Markings</small>
                        <span class="fw-semibold text-dark">{{ $report->missingPetDetail->color_markings ?? '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Collar / Harness</small>
                        <span class="fw-semibold text-dark">
                            {{ $report->missingPetDetail->has_collar ? 'Yes (' . ($report->missingPetDetail->collar_description ?? 'Present') . ')' : 'No' }}
                        </span>
                    </div>
                    @if($report->missingPetDetail->reward_offered)
                    <div class="col-12">
                        <div class="p-2 bg-success bg-opacity-10 border border-success rounded text-success fw-bold small">
                            <i class="bi bi-gift-fill me-1"></i> Reward: {{ $report->missingPetDetail->reward_description ?? 'Yes' }}
                        </div>
                    </div>
                    @endif
                </div>

                @elseif($report->type->value === 'found_pet' && $report->foundPetDetail)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Animal Type / Guess</small>
                        <span class="fw-bold text-capitalize text-dark">{{ $report->foundPetDetail->animal_type }} &bull; {{ $report->foundPetDetail->breed_guess ?? 'Unknown' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Observed Condition</small>
                        <span class="badge bg-info text-dark text-capitalize">{{ $report->foundPetDetail->condition ?? 'Unknown' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Shelter Status</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPetDetail->is_safely_sheltered ? 'Safely sheltered' : 'Free / Not sheltered' }}</span>
                    </div>
                    @if($report->foundPetDetail->immediate_needs)
                    <div class="col-12">
                        <small class="text-muted d-block">Immediate Needs</small>
                        <div class="alert alert-warning border mb-0 small">{{ $report->foundPetDetail->immediate_needs }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Status Timeline --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Status History & Moderation Log</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($report->statusHistories->isEmpty())
                    <p class="text-muted mb-0 small">No history recorded.</p>
                @else
                    <ul class="list-unstyled mb-0 position-relative ps-3 border-start">
                        @foreach($report->statusHistories as $history)
                        <li class="mb-3 position-relative">
                            <i class="bi bi-circle-fill text-primary position-absolute" style="left: -21px; top: 3px; font-size: 0.6rem;"></i>
                            <div class="fw-semibold text-dark">
                                Status:
                                <span class="badge bg-light text-dark border">{{ ucwords(str_replace('_', ' ', $history->new_status)) }}</span>
                            </div>
                            <div class="text-muted small">
                                <span>{{ $history->created_at->format('M d, Y h:i A') }}</span>
                                @if($history->changedByUser)
                                    &bull; by <strong>{{ $history->changedByUser->name }}</strong>
                                @endif
                            </div>
                            @if($history->remarks)
                                <div class="small text-secondary mt-1 bg-light p-2 rounded">{{ $history->remarks }}</div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Sidebar Column --}}
    <div class="col-lg-4">
        {{-- Moderation Controls --}}
        <div class="card border-0 shadow-sm mb-4 border-top border-4 border-primary">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Moderation & State Action</h6>
            </div>
            <div class="card-body p-4 pt-0">
                @if($report->status->value === 'pending_review')
                <div class="d-grid gap-2 mb-3">
                    <form method="POST" action="{{ route('admin.reports.approve', $report) }}"
                          onsubmit="return confirm('Approve this report and publish it to the community?');">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="bi bi-check-circle me-1"></i> Approve & Publish
                        </button>
                    </form>

                    <button type="button" class="btn btn-outline-danger py-2" data-bs-toggle="modal" data-bs-target="#adminRejectModal">
                        <i class="bi bi-x-circle me-1"></i> Reject Report
                    </button>
                </div>
                <hr>
                @endif

                {{-- Status Transition Form --}}
                <form method="POST" action="{{ route('admin.reports.update-status', $report) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="new_status" class="form-label fw-medium small">Transition Status</label>
                        <select name="new_status" id="new_status" class="form-select" required>
                            <option value="">Select new status</option>
                            @foreach($report->status->allowedTransitions() as $targetStatus)
                            <option value="{{ $targetStatus->value }}">{{ $targetStatus->label() }}</option>
                            @endforeach
                        </select>
                        @if(empty($report->status->allowedTransitions()))
                            <div class="form-text text-muted">No further transitions available from current status.</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-medium small">Remarks / Notes</label>
                        <textarea name="remarks" id="remarks" rows="2" class="form-control form-control-sm" placeholder="Optional audit explanation"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100" {{ empty($report->status->allowedTransitions()) ? 'disabled' : '' }}>
                        Update Report Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Reporter Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-person text-primary me-2"></i>Reporter Profile</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px;">
                        {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $report->user->name ?? 'Unknown User' }}</div>
                        <small class="text-muted">{{ $report->user->email ?? 'No email' }}</small>
                    </div>
                </div>
                <div class="small">
                    <div class="mb-2"><strong class="text-muted">Registered Barangay:</strong> {{ $report->user->barangay->name ?? 'None' }}</div>
                    <div class="mb-2"><strong class="text-muted">Member Since:</strong> {{ $report->user->created_at->format('M Y') }}</div>
                    <div><strong class="text-muted">Contact Preference:</strong> <span class="badge bg-light text-dark border">{{ $report->contact_preference }}</span></div>
                </div>
            </div>
        </div>

        {{-- Location Details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt text-primary me-2"></i>Location Details</h6>
            </div>
            <div class="card-body p-4 pt-0 small">
                <div class="mb-2"><strong class="text-muted">Barangay:</strong> {{ $report->barangay->name ?? '—' }}</div>
                @if($report->communityZone)
                <div class="mb-2"><strong class="text-muted">Zone:</strong> {{ $report->communityZone->name }}</div>
                @endif
                @if($report->location_description)
                <div class="mb-2"><strong class="text-muted">Description:</strong> {{ $report->location_description }}</div>
                @endif
                @if($report->latitude && $report->longitude)
                <div>
                    <strong class="text-muted">Coordinates:</strong>
                    <span class="font-monospace">{{ $report->latitude }}, {{ $report->longitude }}</span>
                    <a href="https://maps.google.com/?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-map me-1"></i> View on Google Maps
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Admin Reject Modal --}}
<div class="modal fade" id="adminRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.reports.reject', $report) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Reject Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Please provide the reason for rejecting this report:</p>
                    <div class="mb-3">
                        <label for="admin_rejection_reason" class="form-label fw-medium">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="admin_rejection_reason" name="rejection_reason" rows="3" required
                                  placeholder="State why this report is rejected..."></textarea>
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
