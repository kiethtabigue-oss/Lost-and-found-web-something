<div class="row">
    <div class="col-md-6 mb-3">
        <label for="brand" class="form-label fw-medium">Brand</label>
        <input type="text" class="form-control @error('brand') is-invalid @enderror"
               id="brand" name="brand" value="{{ old('brand', $report->foundPropertyDetail->brand ?? '') }}" placeholder="e.g., Samsung, Nike">
        @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="color" class="form-label fw-medium">Color</label>
        <input type="text" class="form-control @error('color') is-invalid @enderror"
               id="color" name="color" value="{{ old('color', $report->foundPropertyDetail->color ?? '') }}" placeholder="e.g., Black, Silver">
        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="model" class="form-label fw-medium">Model</label>
        <input type="text" class="form-control @error('model') is-invalid @enderror"
               id="model" name="model" value="{{ old('model', $report->foundPropertyDetail->model ?? '') }}" placeholder="e.g., Galaxy S24">
        @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="custody_status" class="form-label fw-medium">Custody Status <span class="text-danger">*</span></label>
        <select class="form-select @error('custody_status') is-invalid @enderror"
                id="custody_status" name="custody_status" required>
            <option value="with_finder" {{ old('custody_status', $report->foundPropertyDetail->custody_status ?? '') === 'with_finder' ? 'selected' : '' }}>With Finder</option>
            <option value="turned_over" {{ old('custody_status', $report->foundPropertyDetail->custody_status ?? '') === 'turned_over' ? 'selected' : '' }}>Turned Over to Office</option>
            <option value="stored" {{ old('custody_status', $report->foundPropertyDetail->custody_status ?? '') === 'stored' ? 'selected' : '' }}>Stored in Safe Location</option>
        </select>
        @error('custody_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-3">
    <label for="distinguishing_marks" class="form-label fw-medium">Distinguishing Marks</label>
    <textarea class="form-control @error('distinguishing_marks') is-invalid @enderror"
              id="distinguishing_marks" name="distinguishing_marks" rows="2"
              placeholder="Scratches, stickers, engravings, etc.">{{ old('distinguishing_marks', $report->foundPropertyDetail->distinguishing_marks ?? '') }}</textarea>
    @error('distinguishing_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="storage_location_hint" class="form-label fw-medium">Storage Location Hint</label>
    <input type="text" class="form-control @error('storage_location_hint') is-invalid @enderror"
           id="storage_location_hint" name="storage_location_hint" value="{{ old('storage_location_hint', $report->foundPropertyDetail->storage_location_hint ?? '') }}"
           placeholder="General area where item is being kept">
    @error('storage_location_hint')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
