<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoundPetDetail extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'report_id',
        'animal_type',
        'breed_guess',
        'color_markings',
        'approximate_age',
        'size',
        'has_collar',
        'collar_description',
        'visible_tag_info',
        'condition',
        'immediate_needs',
        'is_safely_sheltered',
        'shelter_location_hint',
        'verification_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_collar' => 'boolean',
            'is_safely_sheltered' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
