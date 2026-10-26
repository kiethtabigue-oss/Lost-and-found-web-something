<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RecoveryClaim extends Model
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'report_id',
        'claimant_id',
        'claim_description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ClaimStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RecoveryClaim $claim) {
            if (empty($claim->reference_number)) {
                $claim->reference_number = self::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        $ref = "RC-{$date}-{$random}";

        while (self::where('reference_number', $ref)->exists()) {
            $random = strtoupper(Str::random(4));
            $ref = "RC-{$date}-{$random}";
        }

        return $ref;
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
    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * @return HasMany<ClaimEvidence, $this>
     */
    public function evidence(): HasMany
    {
        return $this->hasMany(ClaimEvidence::class, 'claim_id');
    }

    /**
     * @return HasOne<RecoveryTransaction, $this>
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(RecoveryTransaction::class, 'claim_id');
    }
}
