<?php

namespace App\Enums;

enum AccessRequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Access Approved',
            self::REJECTED => 'Request Rejected',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/50',
            self::APPROVED => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/50',
            self::REJECTED => 'bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 border-red-200/50 dark:border-red-800/50',
        };
    }
}
