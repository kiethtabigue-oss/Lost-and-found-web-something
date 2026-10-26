<div class="row">
    <div class="col-md-6 mb-3">
        <label for="animal_type" class="form-label fw-medium">Animal Type <span class="text-danger">*</span></label>
        <select class="form-select @error('animal_type') is-invalid @enderror"
                id="animal_type" name="animal_type" required>
            <option value="">Select animal type</option>
            @php $selectedType = old('animal_type', $report->foundPetDetail->animal_type ?? ''); @endphp
            <option value="dog" {{ $selectedType === 'dog' ? 'selected' : '' }}>Dog</option>
            <option value="cat" {{ $selectedType === 'cat' ? 'selected' : '' }}>Cat</option>
            <option value="bird" {{ $selectedType === 'bird' ? 'selected' : '' }}>Bird</option>
            <option value="rabbit" {{ $selectedType === 'rabbit' ? 'selected' : '' }}>Rabbit</option>
            <option value="fish" {{ $selectedType === 'fish' ? 'selected' : '' }}>Fish</option>
            <option value="other" {{ $selectedType === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('animal_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="breed_guess" class="form-label fw-medium">Breed Guess / Appearance</label>
        <input type="text" class="form-control @error('breed_guess') is-invalid @enderror"
               id="breed_guess" name="breed_guess"
               value="{{ old('breed_guess', $report->foundPetDetail->breed_guess ?? '') }}"
               placeholder="e.g., Looks like an Aspin, Shih Tzu mix, Tabby cat">
        @error('breed_guess')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="color_markings" class="form-label fw-medium">Color & Markings</label>
        <input type="text" class="form-control @error('color_markings') is-invalid @enderror"
               id="color_markings" name="color_markings"
               value="{{ old('color_markings', $report->foundPetDetail->color_markings ?? '') }}"
               placeholder="e.g., Brown with white chest, one folded ear">
        @error('color_markings')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="size" class="form-label fw-medium">Size</label>
        <select class="form-select @error('size') is-invalid @enderror" id="size" name="size">
            <option value="">Select size</option>
            @php $selectedSize = old('size', $report->foundPetDetail->size ?? ''); @endphp
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
        <label for="approximate_age" class="form-label fw-medium">Estimated Age / Stage</label>
        <input type="text" class="form-control @error('approximate_age') is-invalid @enderror"
               id="approximate_age" name="approximate_age"
               value="{{ old('approximate_age', $report->foundPetDetail->approximate_age ?? '') }}"
               placeholder="e.g., Puppy/Kitten, Young adult, Senior">
        @error('approximate_age')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="condition" class="form-label fw-medium">Observed Physical Condition</label>
        <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition">
            <option value="">Select condition</option>
            @php $cond = old('condition', $report->foundPetDetail->condition ?? ''); @endphp
            <option value="healthy" {{ $cond === 'healthy' ? 'selected' : '' }}>Healthy & Active</option>
            <option value="injured" {{ $cond === 'injured' ? 'selected' : '' }}>Injured / Limping</option>
            <option value="malnourished" {{ $cond === 'malnourished' ? 'selected' : '' }}>Malnourished / Weak</option>
            <option value="sick" {{ $cond === 'sick' ? 'selected' : '' }}>Sick / In Distress</option>
            <option value="unknown" {{ $cond === 'unknown' ? 'selected' : '' }}>Unknown</option>
        </select>
        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="hidden" name="has_collar" value="0">
            <input class="form-check-input" type="checkbox" id="has_collar" name="has_collar" value="1"
                   {{ old('has_collar', $report->foundPetDetail->has_collar ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-medium" for="has_collar">
                Found wearing a collar or harness
            </label>
        </div>
        <input type="text" class="form-control @error('collar_description') is-invalid @enderror"
               id="collar_description" name="collar_description"
               value="{{ old('collar_description', $report->foundPetDetail->collar_description ?? '') }}"
               placeholder="Collar color, bell, style...">
        @error('collar_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="visible_tag_info" class="form-label fw-medium">Visible Tag Info</label>
        <input type="text" class="form-control @error('visible_tag_info') is-invalid @enderror"
               id="visible_tag_info" name="visible_tag_info"
               value="{{ old('visible_tag_info', $report->foundPetDetail->visible_tag_info ?? '') }}"
               placeholder="Any visible name or partial phone number on tag">
        <div class="form-text">For safety, keep private details partially redacted if unsure.</div>
        @error('visible_tag_info')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="form-check mb-2">
            <input type="hidden" name="is_safely_sheltered" value="0">
            <input class="form-check-input" type="checkbox" id="is_safely_sheltered" name="is_safely_sheltered" value="1"
                   {{ old('is_safely_sheltered', $report->foundPetDetail->is_safely_sheltered ?? false) ? 'checked' : '' }}>
            <label class="form-check-label fw-medium" for="is_safely_sheltered">
                Pet is currently safely sheltered by finder
            </label>
        </div>
        <input type="text" class="form-control @error('shelter_location_hint') is-invalid @enderror"
               id="shelter_location_hint" name="shelter_location_hint"
               value="{{ old('shelter_location_hint', $report->foundPetDetail->shelter_location_hint ?? '') }}"
               placeholder="General shelter area (e.g., in finder's backyard, at local clinic)">
        @error('shelter_location_hint')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="immediate_needs" class="form-label fw-medium">Immediate Needs</label>
        <textarea class="form-control @error('immediate_needs') is-invalid @enderror"
                  id="immediate_needs" name="immediate_needs" rows="2"
                  placeholder="Needs vet attention, foster shelter, food donations...">{{ old('immediate_needs', $report->foundPetDetail->immediate_needs ?? '') }}</textarea>
        @error('immediate_needs')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
