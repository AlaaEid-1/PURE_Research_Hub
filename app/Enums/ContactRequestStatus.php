<?php

namespace App\Enums;

enum ContactRequestStatus: string
{
    case PENDING = 'pending';
    case REPLIED = 'replied';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'New Inquiry',
            self::REPLIED => 'Replied',
            self::CLOSED => 'Closed Inquiry',
            self::ARCHIVED => 'Archived',
        };
    }
}
