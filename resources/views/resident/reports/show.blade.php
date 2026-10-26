@extends('layouts.resident')

@section('title', $report->title . ' (' . $report->reference_number . ')')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('resident.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('resident.reports.index') }}">My Reports</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $report->reference_number }}</li>
        </ol>
    </nav>
</div>

{{-- Status Alert Banners --}}
@if($report->status->value === 'pending_review')
<div class="alert alert-warning d-flex align-items-center shadow-sm mb-4" role="alert">
    <i class="bi bi-hourglass-split fs-4 me-3"></i>
    <div>
        <strong>Under Barangay Review:</strong> Your report is currently awaiting verification and moderation by barangay administrators before being published.
    </div>
</div>
@elseif($report->status->value === 'rejected')
<div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" role="alert">
    <i class="bi bi-x-circle-fill fs-4 me-3"></i>
    <div>
        <strong>Report Rejected:</strong> This report could not be approved.
        @if($report->moderation_notes)
            <div class="mt-1 small">Reason: {{ $report->moderation_notes }}</div>
        @endif
        <div class="mt-2">
            <a href="{{ route('resident.reports.edit', $report) }}" class="btn btn-sm btn-outline-danger">Edit & Resubmit</a>
        </div>
    </div>
</div>
@elseif($report->status->value === 'recovered' || $report->status->value === 'reunited')
<div class="alert alert-success d-flex align-items-center shadow-sm mb-4" role="alert">
    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
    <div>
        <strong>Successfully Recovered / Reunited!</strong> This case has been marked as resolved.
    </div>
</div>
@endif

<div class="row g-4">
    {{-- Main Content Column --}}
    <div class="col-lg-8">
        {{-- Header Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge {{ $report->type->badgeClass() }} py-2 px-3 fs-6">
                                <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
                            </span>
                            <span class="badge {{ $report->status->badgeClass() }} py-2 px-3 fs-6">
                                {{ $report->status->label() }}
                            </span>
                        </div>
                        <h2 class="fw-bold text-dark mt-2 mb-1">{{ $report->title }}</h2>
                        <div class="text-muted small">
                            <span class="font-monospace fw-semibold me-3"><i class="bi bi-hash"></i>{{ $report->reference_number }}</span>
                            <span class="me-3"><i class="bi bi-clock me-1"></i>Reported {{ $report->created_at->diffForHumans() }}</span>
                            <span><i class="bi bi-eye me-1"></i>{{ $report->is_public ? 'Public Listing' : 'Private' }}</span>
                        </div>
                    </div>
                    @if(in_array($report->status->value, ['draft', 'pending_review', 'rejected']))
                    <div>
                        <a href="{{ route('resident.reports.edit', $report) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Edit Report
                        </a>
                    </div>
                    @endif
                </div>

                <hr class="my-3 text-muted">

                <h5 class="fw-bold mb-2">Description</h5>
                <p class="text-secondary leading-relaxed mb-0" style="white-space: pre-line;">{{ $report->description }}</p>
            </div>
        </div>

        {{-- Photos / Gallery --}}
        @if($report->images->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0"><i class="bi bi-images text-primary me-2"></i>Photos ({{ $report->images->count() }})</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    @foreach($report->images as $image)
                    <div class="col-6 col-md-4">
                        <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank" class="d-block text-decoration-none">
                            <img src="{{ asset('storage/' . $image->file_path) }}"
                                 alt="Report photo"
                                 class="img-fluid rounded shadow-sm object-fit-cover w-100"
                                 style="height: 180px; transition: transform 0.2s;"
                                 onmouseover="this.style.transform='scale(1.03)'"
                                 onmouseout="this.style.transform='scale(1)'">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Type-Specific Details Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0">
                    <i class="bi bi-card-checklist text-primary me-2"></i>
                    @if($report->type->value === 'lost_property' || $report->type->value === 'found_property')
                        Property Details
                    @else
                        Pet Profile Details
                    @endif
                </h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($report->type->value === 'lost_property' && $report->lostPropertyDetail)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Brand</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->brand ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->color ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Model</small>
                        <span class="fw-semibold text-dark">{{ $report->lostPropertyDetail->model ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Estimated Value</small>
                        <span class="fw-semibold text-dark">
                            {{ $report->lostPropertyDetail->estimated_value ? '₱' . number_format($report->lostPropertyDetail->estimated_value, 2) : 'Not specified' }}
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
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->brand ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->color ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Model</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPropertyDetail->model ?? 'Not specified' }}</span>
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
                        <span class="fw-bold fs-5 text-dark">{{ $report->missingPetDetail->pet_name ?? 'Unknown / Unnamed' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Animal Type</small>
                        <span class="fw-semibold text-capitalize text-dark">{{ $report->missingPetDetail->animal_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Breed</small>
                        <span class="fw-semibold text-dark">{{ $report->missingPetDetail->breed ?? 'Mixed / Unknown' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color & Markings</small>
                        <span class="fw-semibold text-dark">{{ $report->missingPetDetail->color_markings ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Approximate Age</small>
                        <span class="fw-semibold text-dark">{{ $report->missingPetDetail->approximate_age ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Size</small>
                        <span class="fw-semibold text-capitalize text-dark">{{ str_replace('_', ' ', $report->missingPetDetail->size ?? 'Not specified') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Collar / Harness</small>
                        <span class="fw-semibold text-dark">
                            {{ $report->missingPetDetail->has_collar ? 'Yes: ' . ($report->missingPetDetail->collar_description ?? 'Collar present') : 'No collar' }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Microchip Hint</small>
                        <span class="fw-semibold text-dark">{{ $report->missingPetDetail->microchip_id_hint ?? 'None' }}</span>
                    </div>
                    @if($report->missingPetDetail->medical_notes)
                    <div class="col-12">
                        <small class="text-muted d-block">Medical Needs</small>
                        <div class="alert alert-light border mb-0 text-dark">{{ $report->missingPetDetail->medical_notes }}</div>
                    </div>
                    @endif
                    @if($report->missingPetDetail->behavioral_notes)
                    <div class="col-12">
                        <small class="text-muted d-block">Behavioral Notes</small>
                        <div class="text-dark">{{ $report->missingPetDetail->behavioral_notes }}</div>
                    </div>
                    @endif
                    @if($report->missingPetDetail->reward_offered)
                    <div class="col-12">
                        <div class="p-3 bg-success bg-opacity-10 border border-success rounded text-success fw-bold">
                            <i class="bi bi-gift-fill me-2"></i> Reward Offered: {{ $report->missingPetDetail->reward_description ?? 'Substantial Reward' }}
                        </div>
                    </div>
                    @endif
                </div>

                @elseif($report->type->value === 'found_pet' && $report->foundPetDetail)
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Animal Type</small>
                        <span class="fw-bold fs-5 text-capitalize text-dark">{{ $report->foundPetDetail->animal_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Breed Guess</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPetDetail->breed_guess ?? 'Unknown' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Color & Markings</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPetDetail->color_markings ?? 'Not specified' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Observed Condition</small>
                        <span class="badge bg-info text-dark text-capitalize">{{ $report->foundPetDetail->condition ?? 'Unknown' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Sheltered Status</small>
                        <span class="fw-semibold text-dark">
                            {{ $report->foundPetDetail->is_safely_sheltered ? 'Safely sheltered by finder' : 'Not sheltered' }}
                        </span>
                    </div>
                    @if($report->foundPetDetail->shelter_location_hint)
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Shelter Area</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPetDetail->shelter_location_hint }}</span>
                    </div>
                    @endif
                    @if($report->foundPetDetail->visible_tag_info)
                    <div class="col-12">
                        <small class="text-muted d-block">Visible Tag Info</small>
                        <span class="fw-semibold text-dark">{{ $report->foundPetDetail->visible_tag_info }}</span>
                    </div>
                    @endif
                    @if($report->foundPetDetail->immediate_needs)
                    <div class="col-12">
                        <small class="text-muted d-block">Immediate Needs</small>
                        <div class="alert alert-warning border mb-0">{{ $report->foundPetDetail->immediate_needs }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Possible Matches (if any) --}}
        @if($report->possibleMatches->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 text-primary">
                    <i class="bi bi-magic me-2"></i>Potential Matches ({{ $report->possibleMatches->count() }})
                </h5>
                <span class="badge bg-primary">Auto-matched</span>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="list-group list-group-flush">
                    @foreach($report->possibleMatches as $match)
                    @php $matched = $match->matchedReport; @endphp
                    @if($matched)
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            @if($matched->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $matched->images->first()->file_path) }}"
                                     class="rounded object-fit-cover" width="56" height="56" alt="Matched item">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:56px; height:56px;">
                                    <i class="bi {{ $matched->type->icon() }} fs-4 text-secondary"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold mb-1">{{ $matched->title }}</h6>
                                <div class="small text-muted">
                                    <span class="badge {{ $matched->type->badgeClass() }} me-1">{{ $matched->type->label() }}</span>
                                    <span>{{ $matched->barangay->name ?? '' }} &bull; {{ $matched->incident_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success mb-1 d-block">{{ round($match->score) }}% Match</span>
                            <span class="small text-muted text-capitalize">{{ $match->status }}</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Timeline / Status History --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="card-title fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Status History</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($report->statusHistories->isEmpty())
                    <p class="text-muted mb-0 small">No status changes recorded yet.</p>
                @else
                    <ul class="list-unstyled mb-0 position-relative ps-3 border-start">
                        @foreach($report->statusHistories as $history)
                        <li class="mb-3 position-relative">
                            <i class="bi bi-circle-fill text-primary position-absolute" style="left: -21px; top: 3px; font-size: 0.6rem;"></i>
                            <div class="fw-semibold text-dark">
                                Status changed to
                                <span class="badge bg-light text-dark border">{{ ucwords(str_replace('_', ' ', $history->new_status)) }}</span>
                            </div>
                            <div class="text-muted small">
                                <span>{{ $history->created_at->format('M d, Y h:i A') }}</span>
                                @if($history->changedByUser)
                                    &bull; by {{ $history->changedByUser->name }}
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

    {{-- Sidebar Information Column --}}
    <div class="col-lg-4">
        {{-- Location & Incident Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt text-primary me-2"></i>Location & Incident</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="mb-3">
                    <small class="text-muted d-block">Barangay</small>
                    <span class="fw-semibold text-dark">{{ $report->barangay->name ?? '—' }}</span>
                </div>
                @if($report->communityZone)
                <div class="mb-3">
                    <small class="text-muted d-block">Zone / Purok</small>
                    <span class="fw-semibold text-dark">{{ $report->communityZone->name }}</span>
                </div>
                @endif
                <div class="mb-3">
                    <small class="text-muted d-block">Date of Incident</small>
                    <span class="fw-semibold text-dark">{{ $report->incident_date->format('F d, Y') }}</span>
                    @if($report->incident_time)
                        <span class="text-muted">at {{ date('h:i A', strtotime($report->incident_time)) }}</span>
                    @endif
                </div>
                @if($report->location_description)
                <div class="mb-3">
                    <small class="text-muted d-block">Specific Location</small>
                    <span class="text-dark">{{ $report->location_description }}</span>
                </div>
                @endif
                @if($report->latitude && $report->longitude)
                <div class="mb-2">
                    <small class="text-muted d-block mb-1">GPS Coordinates</small>
                    <span class="badge bg-light text-dark border font-monospace">
                        {{ $report->latitude }}, {{ $report->longitude }}
                    </span>
                    <a href="https://maps.google.com/?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="btn btn-sm btn-outline-secondary d-block mt-2">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open in Maps
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Contact & Visibility --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>Preferences & Privacy</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="mb-3">
                    <small class="text-muted d-block">Contact Preference</small>
                    <span class="fw-semibold text-dark">
                        @if($report->contact_preference === 'in_app')
                            <i class="bi bi-chat-dots me-1 text-primary"></i> In-App Messaging Only
                        @elseif($report->contact_preference === 'email')
                            <i class="bi bi-envelope me-1 text-primary"></i> Email Contact
                        @else
                            <i class="bi bi-telephone me-1 text-primary"></i> Phone Call / SMS
                        @endif
                    </span>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Visibility</small>
                    <span class="fw-semibold text-dark">
                        @if($report->is_public)
                            <i class="bi bi-globe text-success me-1"></i> Public Community Feed
                        @else
                            <i class="bi bi-lock text-secondary me-1"></i> Private / Admin Only
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Help Card --}}
        <div class="card border-0 bg-primary text-white shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Need Help or Updates?</h6>
                <p class="small text-white-50 mb-3">If you need to update critical details or have questions about moderation, contact your local Barangay admin.</p>
                <a href="{{ route('resident.reports.index') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Return to My Reports
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
