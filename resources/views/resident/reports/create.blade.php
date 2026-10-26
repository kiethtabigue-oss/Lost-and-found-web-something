@extends('layouts.resident')

@section('title', 'Create Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Create a Report</h4>
        <p class="text-muted mb-0">Select a report type to get started.</p>
    </div>
    <a href="{{ route('resident.reports.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to My Reports
    </a>
</div>

{{-- Report Type Tabs --}}
<div class="card mb-4">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-fill p-2 gap-2" id="reportTypeTabs">
            <li class="nav-item">
                <a class="nav-link rounded-3 py-3 {{ $type === 'lost_property' ? 'active' : '' }}"
                   href="{{ route('resident.reports.create', ['type' => 'lost_property']) }}">
                    <i class="bi bi-exclamation-triangle d-block fs-4 mb-1"></i>
                    <span class="small fw-medium">Lost Property</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-3 {{ $type === 'found_property' ? 'active' : '' }}"
                   href="{{ route('resident.reports.create', ['type' => 'found_property']) }}">
                    <i class="bi bi-box-seam d-block fs-4 mb-1"></i>
                    <span class="small fw-medium">Found Property</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-3 {{ $type === 'missing_pet' ? 'active' : '' }}"
                   href="{{ route('resident.reports.create', ['type' => 'missing_pet']) }}">
                    <i class="bi bi-search-heart d-block fs-4 mb-1"></i>
                    <span class="small fw-medium">Missing Pet</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 py-3 {{ $type === 'found_pet' ? 'active' : '' }}"
                   href="{{ route('resident.reports.create', ['type' => 'found_pet']) }}">
                    <i class="bi bi-heart d-block fs-4 mb-1"></i>
                    <span class="small fw-medium">Found Pet</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<form method="POST" action="{{ route('resident.reports.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="row g-4">
        {{-- Main Form --}}
        <div class="col-lg-8">
            {{-- Basic Information --}}
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>Basic Information</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-medium">Report Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="Brief title describing the item or pet" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                  placeholder="Provide a detailed description to help identify the item or pet" required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Type-Specific Details --}}
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-list-ul me-2"></i>Details</div>
                <div class="card-body">
                    @if($type === 'lost_property')
                        @include('resident.reports.partials._lost_property_fields')
                    @elseif($type === 'found_property')
                        @include('resident.reports.partials._found_property_fields')
                    @elseif($type === 'missing_pet')
                        @include('resident.reports.partials._missing_pet_fields')
                    @elseif($type === 'found_pet')
                        @include('resident.reports.partials._found_pet_fields')
                    @endif
                </div>
            </div>

            {{-- Location & Time --}}
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-geo-alt me-2"></i>Location & Time</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="incident_date" class="form-label fw-medium">Date of Incident <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('incident_date') is-invalid @enderror"
                                   id="incident_date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required>
                            @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="incident_time" class="form-label fw-medium">Approximate Time</label>
                            <input type="time" class="form-control @error('incident_time') is-invalid @enderror"
                                   id="incident_time" name="incident_time" value="{{ old('incident_time') }}">
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
                                <option value="{{ $barangay->id }}" {{ old('barangay_id', auth()->user()->barangay_id) == $barangay->id ? 'selected' : '' }}>
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
                                <option value="{{ $zone->id }}" {{ old('community_zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location_description" class="form-label fw-medium">Location Description</label>
                        <input type="text" class="form-control @error('location_description') is-invalid @enderror"
                               id="location_description" name="location_description" value="{{ old('location_description') }}"
                               placeholder="e.g., Near the plaza, in front of the barangay hall">
                    </div>

                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                </div>
            </div>

            {{-- Photo Upload --}}
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-camera me-2"></i>Photos (Max 5)</div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                               name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Upload up to 5 images (JPEG, PNG, or WebP, max 2 MB each).</div>
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
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-chat-dots me-2"></i>Contact Preference</div>
                <div class="card-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="contact_preference"
                               id="contact_in_app" value="in_app" {{ old('contact_preference', 'in_app') === 'in_app' ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact_in_app">In-App Messaging</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="contact_preference"
                               id="contact_email" value="email" {{ old('contact_preference') === 'email' ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact_email">Email</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="contact_preference"
                               id="contact_phone" value="phone" {{ old('contact_preference') === 'phone' ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact_phone">Phone</label>
                    </div>
                </div>
            </div>

            {{-- Visibility --}}
            <div class="card mb-4">
                <div class="card-header"><i class="bi bi-eye me-2"></i>Visibility</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_public"
                               id="is_public" value="1" {{ old('is_public', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public">Make this report publicly visible</label>
                    </div>
                    <div class="form-text mt-2">Public reports can be viewed by all community members.</div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="card border-primary">
                <div class="card-body text-center">
                    <p class="text-muted small mb-3">Your report will be reviewed by a barangay administrator before being published.</p>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-send me-2"></i>Submit Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
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
