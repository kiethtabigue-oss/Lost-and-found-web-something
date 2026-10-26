@extends('layouts.resident')

@section('title', 'Edit Report — ' . $report->reference_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('resident.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('resident.reports.index') }}">My Reports</a></li>
                <li class="breadcrumb-item"><a href="{{ route('resident.reports.show', $report) }}">{{ $report->reference_number }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">Edit Report <span class="text-primary font-monospace">({{ $report->reference_number }})</span></h4>
    </div>
    <a href="{{ route('resident.reports.show', $report) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Report
    </a>
</div>

{{-- Report Type Banner --}}
<div class="alert alert-light border shadow-sm d-flex align-items-center mb-4">
    <span class="badge {{ $report->type->badgeClass() }} py-2 px-3 me-3 fs-6">
        <i class="bi {{ $report->type->icon() }} me-1"></i> {{ $report->type->label() }}
    </span>
    <div>
        <strong>Report Type:</strong> {{ $report->type->label() }} &bull; Current status:
        <span class="badge {{ $report->status->badgeClass() }}">{{ $report->status->label() }}</span>
        <div class="text-muted small">Saving changes will resubmit this report for review.</div>
    </div>
</div>

<form method="POST" action="{{ route('resident.reports.update', $report) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="type" value="{{ $report->type->value }}">

    <div class="row g-4">
        {{-- Main Form --}}
        <div class="col-lg-8">
            {{-- Basic Information --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">Report Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $report->title) }}"
                               placeholder="Brief title describing the item or pet" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $report->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4"
                                  placeholder="Provide a detailed description" required>{{ old('description', $report->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Type-Specific Details --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-list-ul text-primary me-2"></i>Specific Details</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($report->type->value === 'lost_property')
                        @include('resident.reports.partials._lost_property_fields')
                    @elseif($report->type->value === 'found_property')
                        @include('resident.reports.partials._found_property_fields')
                    @elseif($report->type->value === 'missing_pet')
                        @include('resident.reports.partials._missing_pet_fields')
                    @elseif($report->type->value === 'found_pet')
                        @include('resident.reports.partials._found_pet_fields')
                    @endif
                </div>
            </div>

            {{-- Location & Time --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-geo-alt text-primary me-2"></i>Location & Time</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="incident_date" class="form-label fw-medium">Date of Incident <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('incident_date') is-invalid @enderror"
                                   id="incident_date" name="incident_date"
                                   value="{{ old('incident_date', $report->incident_date->format('Y-m-d')) }}" required>
                            @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="incident_time" class="form-label fw-medium">Approximate Time</label>
                            <input type="time" class="form-control @error('incident_time') is-invalid @enderror"
                                   id="incident_time" name="incident_time"
                                   value="{{ old('incident_time', $report->incident_time ? date('H:i', strtotime($report->incident_time)) : '') }}">
                            @error('incident_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="barangay_id" class="form-label fw-medium">Barangay <span class="text-danger">*</span></label>
                            <select class="form-select @error('barangay_id') is-invalid @enderror"
                                    id="barangay_id" name="barangay_id" required>
                                <option value="">Select barangay</option>
                                @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}" {{ old('barangay_id', $report->barangay_id) == $barangay->id ? 'selected' : '' }}>
                                    {{ $barangay->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('barangay_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="community_zone_id" class="form-label fw-medium">Community Zone</label>
                            <select class="form-select @error('community_zone_id') is-invalid @enderror"
                                    id="community_zone_id" name="community_zone_id">
                                <option value="">Select zone (optional)</option>
                                @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('community_zone_id', $report->community_zone_id) == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location_description" class="form-label fw-medium">Location Description</label>
                        <input type="text" class="form-control @error('location_description') is-invalid @enderror"
                               id="location_description" name="location_description"
                               value="{{ old('location_description', $report->location_description) }}"
                               placeholder="e.g., Near the plaza, in front of the barangay hall">
                    </div>

                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $report->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $report->longitude) }}">
                </div>
            </div>

            {{-- Existing Photos & Upload --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-camera text-primary me-2"></i>Photos</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($report->images->isNotEmpty())
                    <label class="form-label fw-medium d-block mb-2">Current Photos:</label>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        @foreach($report->images as $img)
                        <div class="position-relative border rounded p-1" style="width: 100px;">
                            <img src="{{ asset('storage/' . $img->file_path) }}" class="rounded w-100 object-fit-cover" style="height: 80px;" alt="Image">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 rounded-circle d-flex align-items-center justify-content-center delete-img-btn"
                                    style="width: 22px; height: 22px;"
                                    data-url="{{ route('resident.reports.destroy-image', [$report, $img]) }}"
                                    title="Delete image">
                                &times;
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="images" class="form-label fw-medium">Upload Additional Photos</label>
                        <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                               name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Upload up to 5 images total (JPEG, PNG, or WebP, max 2 MB each).</div>
                        @error('images')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div id="imagePreviewContainer" class="d-flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Contact Preference --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-chat-dots text-primary me-2"></i>Contact Preference</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="contact_preference"
                               id="contact_in_app" value="in_app" {{ old('contact_preference', $report->contact_preference) === 'in_app' ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact_in_app">In-App Messaging</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="contact_preference"
                               id="contact_email" value="email" {{ old('contact_preference', $report->contact_preference) === 'email' ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact_email">Email</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="contact_preference"
                               id="contact_phone" value="phone" {{ old('contact_preference', $report->contact_preference) === 'phone' ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact_phone">Phone</label>
                    </div>
                </div>
            </div>

            {{-- Visibility --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-eye text-primary me-2"></i>Visibility</h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_public"
                               id="is_public" value="1" {{ old('is_public', $report->is_public) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public">Make this report publicly visible</label>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-2">
                        <i class="bi bi-check2-circle me-1"></i> Update Report
                    </button>
                    <a href="{{ route('resident.reports.show', $report) }}" class="btn btn-outline-secondary w-100">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Hidden Form for Deleting Images --}}
<form id="deleteImageForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-img-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this photo?')) {
            const form = document.getElementById('deleteImageForm');
            form.action = this.dataset.url;
            form.submit();
        }
    });
});

document.getElementById('images')?.addEventListener('change', function() {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    Array.from(this.files).forEach(file => {
        if (file.size > 2 * 1024 * 1024) return;
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'rounded border';
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
