<?php

namespace App\Http\Requests;

use App\Enums\ReportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::enum(ReportType::class)],
            'category_id' => ['required', 'exists:report_categories,id'],
            'barangay_id' => ['required', 'exists:barangays,id'],
            'community_zone_id' => ['nullable', 'exists:community_zones,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'incident_time' => ['nullable', 'date_format:H:i'],
            'location_description' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'contact_preference' => ['required', Rule::in(['in_app', 'email', 'phone'])],
            'is_public' => ['boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];

        $type = $this->input('type');

        // Add type-specific detail rules
        if ($type === ReportType::LostProperty->value) {
            $rules = array_merge($rules, $this->lostPropertyRules());
        } elseif ($type === ReportType::FoundProperty->value) {
            $rules = array_merge($rules, $this->foundPropertyRules());
        } elseif ($type === ReportType::MissingPet->value) {
            $rules = array_merge($rules, $this->missingPetRules());
        } elseif ($type === ReportType::FoundPet->value) {
            $rules = array_merge($rules, $this->foundPetRules());
        }

        return $rules;
    }

    /**
     * @return array<string, mixed>
     */
    private function lostPropertyRules(): array
    {
        return [
            'brand' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'distinguishing_marks' => ['nullable', 'string', 'max:500'],
            'serial_number_hint' => ['nullable', 'string', 'max:100'],
            'estimated_value' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function foundPropertyRules(): array
    {
        return [
            'brand' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'distinguishing_marks' => ['nullable', 'string', 'max:500'],
            'custody_status' => ['required', Rule::in(['with_finder', 'turned_over', 'stored'])],
            'storage_location_hint' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function missingPetRules(): array
    {
        return [
            'pet_name' => ['nullable', 'string', 'max:100'],
            'animal_type' => ['required', Rule::in(['dog', 'cat', 'bird', 'rabbit', 'fish', 'other'])],
            'breed' => ['nullable', 'string', 'max:100'],
            'color_markings' => ['nullable', 'string', 'max:200'],
            'approximate_age' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', Rule::in(['small', 'medium', 'large', 'extra_large'])],
            'has_collar' => ['boolean'],
            'collar_description' => ['nullable', 'string', 'max:200'],
            'microchip_id_hint' => ['nullable', 'string', 'max:100'],
            'medical_notes' => ['nullable', 'string', 'max:500'],
            'behavioral_notes' => ['nullable', 'string', 'max:500'],
            'reward_offered' => ['boolean'],
            'reward_description' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function foundPetRules(): array
    {
        return [
            'animal_type' => ['required', Rule::in(['dog', 'cat', 'bird', 'rabbit', 'fish', 'other'])],
            'breed_guess' => ['nullable', 'string', 'max:100'],
            'color_markings' => ['nullable', 'string', 'max:200'],
            'approximate_age' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', Rule::in(['small', 'medium', 'large', 'extra_large'])],
            'has_collar' => ['boolean'],
            'collar_description' => ['nullable', 'string', 'max:200'],
            'visible_tag_info' => ['nullable', 'string', 'max:200'],
            'condition' => ['nullable', Rule::in(['healthy', 'injured', 'malnourished', 'sick', 'unknown'])],
            'immediate_needs' => ['nullable', 'string', 'max:500'],
            'is_safely_sheltered' => ['boolean'],
            'shelter_location_hint' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'incident_date.before_or_equal' => 'The incident date cannot be in the future.',
            'images.max' => 'You may upload a maximum of 5 images.',
            'images.*.max' => 'Each image must not exceed 2 MB.',
            'images.*.mimes' => 'Images must be JPEG, PNG, or WebP.',
        ];
    }
}
