<?php

namespace App\Enums;

enum ConversationType: string
{
    case ACCESS_REQUEST = 'access_request';
    case GENERAL_INQUIRY = 'general_inquiry';

    public function label(): string
    {
        return match ($this) {
            self::ACCESS_REQUEST => 'Access Request',
            self::GENERAL_INQUIRY => 'General Inquiry',
        };
    }
}
