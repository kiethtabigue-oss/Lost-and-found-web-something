<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryTransaction extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'claim_id',
        'report_id',
        'handover_method',
        'handover_notes',
        'handover_date',
        'confirmed_by_finder',
        'confirmed_by_owner',
        'finder_confirmed_at',
        'owner_confirmed_at',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'handover_date' => 'datetime',
            'finder_confirmed_at' => 'datetime',
            'owner_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<RecoveryClaim, $this>
     */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(RecoveryClaim::class, 'claim_id');
    }

    /**
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function finderConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_finder');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function ownerConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_owner');
    }

    public function isFullyConfirmed(): bool
    {
        return $this->finder_confirmed_at !== null && $this->owner_confirmed_at !== null;
    }
}
