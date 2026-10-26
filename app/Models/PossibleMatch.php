<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PossibleMatch extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'source_report_id',
        'matched_report_id',
        'score',
        'matching_factors',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'matching_factors' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Report, $this>
     */
    public function sourceReport(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'source_report_id');
    }

    /**
     * @return BelongsTo<Report, $this>
     */
    public function matchedReport(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'matched_report_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
