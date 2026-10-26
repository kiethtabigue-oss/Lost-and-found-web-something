<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Active = 'active';
    case PossibleMatch = 'possible_match';
    case ClaimPending = 'claim_pending';
    case UnderVerification = 'under_verification';
    case Recovered = 'recovered';
    case Reunited = 'reunited';
    case Closed = 'closed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingReview => 'Pending Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Active => 'Active',
            self::PossibleMatch => 'Possible Match',
            self::ClaimPending => 'Claim Pending',
            self::UnderVerification => 'Under Verification',
            self::Recovered => 'Recovered',
            self::Reunited => 'Reunited with Owner',
            self::Closed => 'Closed',
            self::Archived => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge-draft',
            self::PendingReview => 'badge-pending',
            self::Approved => 'badge-approved',
            self::Rejected => 'badge-rejected',
            self::Active => 'badge-active',
            self::PossibleMatch => 'badge-possible-match',
            self::ClaimPending => 'badge-claim-pending',
            self::UnderVerification => 'badge-under-verification',
            self::Recovered => 'badge-recovered',
            self::Reunited => 'badge-reunited',
            self::Closed => 'badge-closed',
            self::Archived => 'badge-archived',
        };
    }

    /**
     * Valid transitions from each status.
     *
     * @return array<ReportStatus>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::PendingReview],
            self::PendingReview => [self::Approved, self::Active, self::Rejected],
            self::Approved => [self::Active],
            self::Rejected => [self::PendingReview, self::Archived],
            self::Active => [self::PossibleMatch, self::ClaimPending, self::Closed, self::Archived],
            self::PossibleMatch => [self::ClaimPending, self::Active, self::Closed],
            self::ClaimPending => [self::UnderVerification, self::Active],
            self::UnderVerification => [self::Recovered, self::Reunited, self::Active],
            self::Recovered => [self::Closed],
            self::Reunited => [self::Closed],
            self::Closed => [self::Archived],
            self::Archived => [],
        };
    }

    /**
     * Check if transition to given status is allowed.
     */
    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
