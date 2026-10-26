<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportImage extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'report_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'is_primary',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the public asset URL for the report image.
     */
    public function getUrlAttribute(): string
    {
        if (! $this->file_path) {
            return '';
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }
}
