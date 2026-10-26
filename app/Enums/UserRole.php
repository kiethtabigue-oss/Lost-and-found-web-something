<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case BarangayAdmin = 'barangay_admin';
    case Resident = 'resident';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::BarangayAdmin => 'Barangay Administrator',
            self::Resident => 'Registered Resident',
        };
    }
}
