<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case AdditionalInfoRequested = 'additional_info_requested';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case HandoverScheduled = 'handover_scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::AdditionalInfoRequested => 'Additional Info Requested',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::HandoverScheduled => 'Handover Scheduled',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::UnderReview => 'bg-info',
            self::AdditionalInfoRequested => 'bg-secondary',
            self::Approved => 'bg-success',
            self::Rejected => 'bg-danger',
            self::HandoverScheduled => 'bg-primary',
            self::Completed => 'bg-success',
            self::Cancelled => 'bg-secondary',
        };
    }
}
