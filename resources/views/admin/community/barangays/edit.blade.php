@extends('layouts.admin')

@section('title', 'Modify Barangay — ' . $barangay->name)
@section('page-title', 'Modify Community Barangay')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Modify Barangay: {{ $barangay->name }}</h4>
                <p class="text-muted small mb-0">Update community barangay name, jurisdiction code, coordinates, and operational status.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.barangays.show', $barangay) }}" class="btn btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> View Details
                </a>
                <a href="{{ route('admin.barangays.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle text-primary me-2"></i>Edit Barangay Details</h6>
                <span class="badge {{ $barangay->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }}">
                    {{ $barangay->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.barangays.update', $barangay) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Barangay Name --}}
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-medium">Barangay Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $barangay->name) }}"
                                   placeholder="e.g., Barangay San Antonio" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Changing the barangay name will update all citizen displays, reports, and audit trails.</div>
                        </div>

                        {{-- Code --}}
                        <div class="col-md-4">
                            <label for="code" class="form-label fw-medium">Barangay Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $barangay->code) }}"
                                   placeholder="e.g., BRGY-001" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Unique system code.</div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="form-label fw-medium">Description / Coverage Area</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Brief overview of location landmarks, coverage, or administrative boundaries">{{ old('description', $barangay->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Coordinates --}}
                        <div class="col-md-6">
                            <label for="latitude" class="form-label fw-medium">Center Latitude</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                <input type="number" step="0.0000001" min="-90" max="90"
                                       class="form-control font-monospace @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude', $barangay->latitude) }}"
                                       placeholder="14.5995000">
                                @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="longitude" class="form-label fw-medium">Center Longitude</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                <input type="number" step="0.0000001" min="-180" max="180"
                                       class="form-control font-monospace @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude', $barangay->longitude) }}"
                                       placeholder="120.9842000">
                                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_active" name="is_active" value="1" {{ old('is_active', $barangay->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">
                                    Barangay Jurisdiction is Active
                                </label>
                            </div>
                            <small class="text-muted d-block ms-5">When inactive, new registrations for this barangay are paused, but existing reports and records remain accessible.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span class="text-muted small">
                            <i class="bi bi-clock-history me-1"></i> Created on {{ $barangay->created_at->format('M d, Y') }}
                        </span>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.barangays.show', $barangay) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
