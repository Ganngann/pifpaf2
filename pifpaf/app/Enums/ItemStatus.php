<?php

namespace App\Enums;

enum ItemStatus: string
{
    case AVAILABLE = 'available';
    case UNPUBLISHED = 'unpublished';
    case SOLD = 'sold';

    public static function getTextFor(self $status): string
    {
        return match ($status) {
            self::AVAILABLE => 'En ligne',
            self::UNPUBLISHED => 'Hors ligne',
            self::SOLD => 'Vendu',
        };
    }
}
