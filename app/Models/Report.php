<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'reference_number',
        'user_id',
        'category_id',
        'barangay_id',
        'community_zone_id',
        'type',
        'title',
        'description',
        'incident_date',
        'incident_time',
        'location_description',
        'latitude',
        'longitude',
        'status',
        'contact_preference',
        'is_public',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ReportType::class,
            'status' => ReportStatus::class,
            'incident_date' => 'date',
            'moderated_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_public' => 'boolean',
        ];
    }

    /* ── Boot ──────────────────────────────────────────── */

    protected static function booted(): void
    {
        static::creating(function (Report $report) {
            if (empty($report->reference_number)) {
                $report->reference_number = self::generateReferenceNumber($report->type);
            }
        });
    }

    /**
     * Generate a unique reference number like LP-20261025-A3X9.
     */
    public static function generateReferenceNumber(ReportType|string $type): string
    {
        $prefix = match ($type instanceof ReportType ? $type : ReportType::from($type)) {
            ReportType::LostProperty => 'LP',
            ReportType::FoundProperty => 'FP',
            ReportType::MissingPet => 'MP',
            ReportType::FoundPet => 'FA',
        };

        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        $ref = "{$prefix}-{$date}-{$random}";

        // Ensure uniqueness
        while (self::where('reference_number', $ref)->exists()) {
            $random = strtoupper(Str::random(4));
            $ref = "{$prefix}-{$date}-{$random}";
        }

        return $ref;
    }

    /* ── Relationships ─────────────────────────────────── */

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<ReportCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<Barangay, $this>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * @return BelongsTo<CommunityZone, $this>
     */
    public function communityZone(): BelongsTo
    {
        return $this->belongsTo(CommunityZone::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * @return HasMany<ReportImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ReportImage::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<ReportStatusHistory, $this>
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class)->orderByDesc('created_at');
    }

    /**
     * @return HasOne<LostPropertyDetail, $this>
     */
    public function lostPropertyDetail(): HasOne
    {
        return $this->hasOne(LostPropertyDetail::class);
    }

    /**
     * @return HasOne<FoundPropertyDetail, $this>
     */
    public function foundPropertyDetail(): HasOne
    {
        return $this->hasOne(FoundPropertyDetail::class);
    }

    /**
     * @return HasOne<MissingPetDetail, $this>
     */
    public function missingPetDetail(): HasOne
    {
        return $this->hasOne(MissingPetDetail::class);
    }

    /**
     * @return HasOne<FoundPetDetail, $this>
     */
    public function foundPetDetail(): HasOne
    {
        return $this->hasOne(FoundPetDetail::class);
    }

    /**
     * @return HasMany<PossibleMatch, $this>
     */
    public function possibleMatches(): HasMany
    {
        return $this->hasMany(PossibleMatch::class, 'source_report_id');
    }

    /**
     * @return HasMany<PossibleMatch, $this>
     */
    public function matchedBy(): HasMany
    {
        return $this->hasMany(PossibleMatch::class, 'matched_report_id');
    }

    /**
     * @return HasMany<RecoveryClaim, $this>
     */
    public function claims(): HasMany
    {
        return $this->hasMany(RecoveryClaim::class);
    }

    /**
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * @return HasMany<ReportFlag, $this>
     */
    public function flags(): HasMany
    {
        return $this->hasMany(ReportFlag::class);
    }

    /* ── Helpers ────────────────────────────────────────── */

    /**
     * Get the type-specific detail relationship.
     */
    public function detail(): HasOne
    {
        return match ($this->type) {
            ReportType::LostProperty => $this->lostPropertyDetail(),
            ReportType::FoundProperty => $this->foundPropertyDetail(),
            ReportType::MissingPet => $this->missingPetDetail(),
            ReportType::FoundPet => $this->foundPetDetail(),
        };
    }

    public function primaryImage(): ?ReportImage
    {
        return $this->images->firstWhere('is_primary', true)
            ?? $this->images->first();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /* ── Scopes ─────────────────────────────────────────── */

    /**
     * @param  Builder<Report>  $query
     */
    public function scopePubliclyVisible($query): void
    {
        $query->where('is_public', true)
            ->whereIn('status', [
                ReportStatus::Approved,
                ReportStatus::Active,
                ReportStatus::PossibleMatch,
                ReportStatus::ClaimPending,
            ]);
    }

    /**
     * @param  Builder<Report>  $query
     */
    public function scopeOfType($query, ReportType|string $type): void
    {
        $query->where('type', $type instanceof ReportType ? $type->value : $type);
    }

    /**
     * @param  Builder<Report>  $query
     */
    public function scopeInBarangay($query, int $barangayId): void
    {
        $query->where('barangay_id', $barangayId);
    }
}
