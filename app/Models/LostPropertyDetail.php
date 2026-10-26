<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LostPropertyDetail extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'report_id',
        'brand',
        'color',
        'model',
        'distinguishing_marks',
        'serial_number_hint',
        'estimated_value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
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
