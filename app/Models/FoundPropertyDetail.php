<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoundPropertyDetail extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'report_id',
        'brand',
        'color',
        'model',
        'distinguishing_marks',
        'custody_status',
        'storage_location_hint',
        'turned_over_to_office',
        'verification_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'turned_over_to_office' => 'boolean',
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
