<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimEvidence extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'claim_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'description',
        'is_confidential',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_confidential' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<RecoveryClaim, $this>
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(RecoveryClaim::class, 'claim_id');
    }
}
