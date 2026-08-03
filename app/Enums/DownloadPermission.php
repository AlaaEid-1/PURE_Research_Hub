<?php

namespace App\Enums;

enum DownloadPermission: string
{
    case FREE = 'free';
    case REQUEST_ACCESS = 'request_access';
    case CONTACT_AUTHOR = 'contact_author';
    case RESTRICTED = 'restricted';

    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Open Access Research',
            self::REQUEST_ACCESS => 'Request PDF Access',
            self::CONTACT_AUTHOR => 'Contact Researcher',
            self::RESTRICTED => 'Restricted Research',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::FREE => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-800/50',
            self::REQUEST_ACCESS => 'bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-800/50',
            self::CONTACT_AUTHOR => 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border-blue-200/50 dark:border-blue-800/50',
            self::RESTRICTED => 'bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 border-red-200/50 dark:border-red-800/50',
        };
    }
}
