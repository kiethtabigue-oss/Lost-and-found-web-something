<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissingPetDetail extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'report_id',
        'pet_name',
        'animal_type',
        'breed',
        'color_markings',
        'approximate_age',
        'size',
        'has_collar',
        'collar_description',
        'microchip_id_hint',
        'medical_notes',
        'behavioral_notes',
        'reward_offered',
        'reward_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_collar' => 'boolean',
            'reward_offered' => 'boolean',
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
