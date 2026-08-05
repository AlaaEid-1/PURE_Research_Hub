<?php

namespace App\Enums;

enum ConversationStatus: string
{
    case OPEN = 'open';
    case REPLIED = 'replied';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::REPLIED => 'Replied',
            self::CLOSED => 'Closed',
        };
    }
}
