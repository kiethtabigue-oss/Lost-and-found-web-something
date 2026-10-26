<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'role_id',
        'barangay_id',
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    /** @var list<string> */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /* ── Relationships ─────────────────────────────────── */

    /**
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return BelongsTo<Barangay, $this>
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * @return HasOne<UserProfile, $this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @return HasMany<Report, $this>
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * @return HasMany<RecoveryClaim, $this>
     */
    public function claims(): HasMany
    {
        return $this->hasMany(RecoveryClaim::class, 'claimant_id');
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /* ── Role Helpers ──────────────────────────────────── */

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === UserRole::SuperAdmin->value;
    }

    public function isBarangayAdmin(): bool
    {
        return $this->role?->slug === UserRole::BarangayAdmin->value;
    }

    public function isResident(): bool
    {
        return $this->role?->slug === UserRole::Resident->value;
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isBarangayAdmin();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user can manage resources in a specific barangay.
     */
    public function canManageBarangay(int $barangayId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->isBarangayAdmin() && $this->barangay_id === $barangayId;
    }
}
