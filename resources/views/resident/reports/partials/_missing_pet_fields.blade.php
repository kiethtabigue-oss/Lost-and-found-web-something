<div class="row">
    <div class="col-md-6 mb-3">
        <label for="pet_name" class="form-label fw-medium">Pet Name</label>
        <input type="text" class="form-control @error('pet_name') is-invalid @enderror"
               id="pet_name" name="pet_name" value="{{ old('pet_name', $report->missingPetDetail->pet_name ?? '') }}" placeholder="e.g., Bella, Max">
        @error('pet_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="animal_type" class="form-label fw-medium">Animal Type <span class="text-danger">*</span></label>
        <select class="form-select @error('animal_type') is-invalid @enderror"
                id="animal_type" name="animal_type" required>
            <option value="">Select type</option>
            @php $selectedType = old('animal_type', $report->missingPetDetail->animal_type ?? ''); @endphp
            <option value="dog" {{ $selectedType === 'dog' ? 'selected' : '' }}>Dog</option>
            <option value="cat" {{ $selectedType === 'cat' ? 'selected' : '' }}>Cat</option>
            <option value="bird" {{ $selectedType === 'bird' ? 'selected' : '' }}>Bird</option>
            <option value="rabbit" {{ $selectedType === 'rabbit' ? 'selected' : '' }}>Rabbit</option>
            <option value="fish" {{ $selectedType === 'fish' ? 'selected' : '' }}>Fish</option>
            <option value="other" {{ $selectedType === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('animal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="breed" class="form-label fw-medium">Breed</label>
        <input type="text" class="form-control @error('breed') is-invalid @enderror"
               id="breed" name="breed" value="{{ old('breed', $report->missingPetDetail->breed ?? '') }}" placeholder="e.g., Golden Retriever, Siamese, Mixed">
        @error('breed')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="color_markings" class="form-label fw-medium">Color & Markings</label>
        <input type="text" class="form-control @error('color_markings') is-invalid @enderror"
               id="color_markings" name="color_markings" value="{{ old('color_markings', $report->missingPetDetail->color_markings ?? '') }}" placeholder="e.g., White with brown patches, black spots">
        @error('color_markings')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="approximate_age" class="form-label fw-medium">Approximate Age</label>
        <input type="text" class="form-control @error('approximate_age') is-invalid @enderror"
               id="approximate_age" name="approximate_age" value="{{ old('approximate_age', $report->missingPetDetail->approximate_age ?? '') }}" placeholder="e.g., 2 years, 6 months">
        @error('approximate_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="size" class="form-label fw-medium">Size</label>
        <select class="form-select @error('size') is-invalid @enderror" id="size" name="size">
            <option value="">Select size</option>
            @php $selectedSize = old('size', $report->missingPetDetail->size ?? ''); @endphp
            <option value="small" {{ $selectedSize === 'small' ? 'selected' : '' }}>Small (under 10 kg)</option>
            <option value="medium" {{ $selectedSize === 'medium' ? 'selected' : '' }}>Medium (10-25 kg)</option>
            <option value="large" {{ $selectedSize === 'large' ? 'selected' : '' }}>Large (25-45 kg)</option>
            <option value="extra_large" {{ $selectedSize === 'extra_large' ? 'selected' : '' }}>Extra Large (45+ kg)</option>
        </select>
        @error('size')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="hidden" name="has_collar" value="0">
            <input class="form-check-input" type="checkbox" id="has_collar" name="has_collar" value="1"
                   {{ old('has_collar', $report->missingPetDetail->has_collar ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-medium" for="has_collar">
                Pet is wearing a collar / harness
            </label>
        </div>
        <input type="text" class="form-control @error('collar_description') is-invalid @enderror"
               id="collar_description" name="collar_description"
               value="{{ old('collar_description', $report->missingPetDetail->collar_description ?? '') }}"
               placeholder="Collar color, tag, or description">
        @error('collar_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="microchip_id_hint" class="form-label fw-medium">Microchip ID Hint</label>
        <input type="text" class="form-control @error('microchip_id_hint') is-invalid @enderror"
               id="microchip_id_hint" name="microchip_id_hint"
               value="{{ old('microchip_id_hint', $report->missingPetDetail->microchip_id_hint ?? '') }}"
               placeholder="Last 4 digits or microchip company">
        <div class="form-text">For security, avoid posting the complete microchip number.</div>
        @error('microchip_id_hint')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="medical_notes" class="form-label fw-medium">Medical Needs / Notes</label>
        <textarea class="form-control @error('medical_notes') is-invalid @enderror"
                  id="medical_notes" name="medical_notes" rows="2"
                  placeholder="Requires daily medication, allergies, special diet...">{{ old('medical_notes', $report->missingPetDetail->medical_notes ?? '') }}</textarea>
        @error('medical_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="behavioral_notes" class="form-label fw-medium">Behavioral Notes</label>
        <textarea class="form-control @error('behavioral_notes') is-invalid @enderror"
                  id="behavioral_notes" name="behavioral_notes" rows="2"
                  placeholder="Friendly, shy, timid around men, responds to name...">{{ old('behavioral_notes', $report->missingPetDetail->behavioral_notes ?? '') }}</textarea>
        @error('behavioral_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="card bg-light border-0 mb-3 p-3">
    <div class="form-check mb-2">
        <input type="hidden" name="reward_offered" value="0">
        <input class="form-check-input" type="checkbox" id="reward_offered" name="reward_offered" value="1"
               {{ old('reward_offered', $report->missingPetDetail->reward_offered ?? false) ? 'checked' : '' }}>
        <label class="form-check-label fw-bold text-success" for="reward_offered">
            <i class="bi bi-gift me-1"></i> Reward Offered for Safe Return
        </label>
    </div>
    <input type="text" class="form-control @error('reward_description') is-invalid @enderror"
           id="reward_description" name="reward_description"
           value="{{ old('reward_description', $report->missingPetDetail->reward_description ?? '') }}"
           placeholder="e.g., ₱5,000 cash reward / Substantial reward">
    @error('reward_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
