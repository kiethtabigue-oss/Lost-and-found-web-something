@extends('layouts.admin')

@section('title', 'Add New Barangay')
@section('page-title', 'Add Community Barangay')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-building-add text-primary me-2"></i>Add Community Barangay</h4>
                <p class="text-muted small mb-0">Create a new barangay jurisdiction for community tracking and recovery moderation.</p>
            </div>
            <a href="{{ route('admin.barangays.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Barangays
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle text-primary me-2"></i>Barangay Information</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.barangays.store') }}">
                    @csrf

                    <div class="row g-3">
                        {{-- Barangay Name --}}
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-medium">Barangay Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g., Barangay San Antonio" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Official community name displayed across citizen reports and alerts.</div>
                        </div>

                        {{-- Code --}}
                        <div class="col-md-4">
                            <label for="code" class="form-label fw-medium">Barangay Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code') }}"
                                   placeholder="e.g., BRGY-006" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Unique system identifier.</div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="form-label fw-medium">Description / Coverage Area</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Brief overview of location landmarks, coverage, or administrative boundaries">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Coordinates --}}
                        <div class="col-md-6">
                            <label for="latitude" class="form-label fw-medium">Center Latitude</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                <input type="number" step="0.0000001" min="-90" max="90"
                                       class="form-control font-monospace @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude', '14.5995000') }}"
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
                                       id="longitude" name="longitude" value="{{ old('longitude', '120.9842000') }}"
                                       placeholder="120.9842000">
                                @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">
                                    Set this Barangay as Active immediately
                                </label>
                            </div>
                            <small class="text-muted d-block ms-5">Active barangays allow resident registration, report filing, and community zone management.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.barangays.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Create Barangay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
