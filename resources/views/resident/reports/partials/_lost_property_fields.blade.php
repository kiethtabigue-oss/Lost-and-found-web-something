<div class="row">
    <div class="col-md-6 mb-3">
        <label for="brand" class="form-label fw-medium">Brand</label>
        <input type="text" class="form-control @error('brand') is-invalid @enderror"
               id="brand" name="brand" value="{{ old('brand', $report->lostPropertyDetail->brand ?? '') }}" placeholder="e.g., Samsung, Nike">
        @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="color" class="form-label fw-medium">Color</label>
        <input type="text" class="form-control @error('color') is-invalid @enderror"
               id="color" name="color" value="{{ old('color', $report->lostPropertyDetail->color ?? '') }}" placeholder="e.g., Black, Silver">
        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="model" class="form-label fw-medium">Model</label>
        <input type="text" class="form-control @error('model') is-invalid @enderror"
               id="model" name="model" value="{{ old('model', $report->lostPropertyDetail->model ?? '') }}" placeholder="e.g., Galaxy S24, Air Max">
        @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="estimated_value" class="form-label fw-medium">Estimated Value (₱)</label>
        <input type="number" step="0.01" class="form-control @error('estimated_value') is-invalid @enderror"
               id="estimated_value" name="estimated_value" value="{{ old('estimated_value', $report->lostPropertyDetail->estimated_value ?? '') }}" placeholder="0.00">
        @error('estimated_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-3">
    <label for="distinguishing_marks" class="form-label fw-medium">Distinguishing Marks</label>
    <textarea class="form-control @error('distinguishing_marks') is-invalid @enderror"
              id="distinguishing_marks" name="distinguishing_marks" rows="2"
              placeholder="Scratches, stickers, engravings, etc.">{{ old('distinguishing_marks', $report->lostPropertyDetail->distinguishing_marks ?? '') }}</textarea>
    @error('distinguishing_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="serial_number_hint" class="form-label fw-medium">Serial Number Hint</label>
    <input type="text" class="form-control @error('serial_number_hint') is-invalid @enderror"
           id="serial_number_hint" name="serial_number_hint" value="{{ old('serial_number_hint', $report->lostPropertyDetail->serial_number_hint ?? '') }}"
           placeholder="Partial serial number (do not share full serial)">
    <div class="form-text">For security, only share a partial serial number.</div>
    @error('serial_number_hint')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
