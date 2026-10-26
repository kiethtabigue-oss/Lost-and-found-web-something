<?php

namespace App\Enums;

enum ReportType: string
{
    case LostProperty = 'lost_property';
    case FoundProperty = 'found_property';
    case MissingPet = 'missing_pet';
    case FoundPet = 'found_pet';

    public function label(): string
    {
        return match ($this) {
            self::LostProperty => 'Lost Property',
            self::FoundProperty => 'Found Property',
            self::MissingPet => 'Missing Pet',
            self::FoundPet => 'Found Pet',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LostProperty => 'badge-lost-property',
            self::FoundProperty => 'badge-found-property',
            self::MissingPet => 'badge-missing-pet',
            self::FoundPet => 'badge-found-pet',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LostProperty => 'bi-exclamation-triangle',
            self::FoundProperty => 'bi-box-seam',
            self::MissingPet => 'bi-search-heart',
            self::FoundPet => 'bi-heart',
        };
    }
}
