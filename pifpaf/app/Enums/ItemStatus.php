<?php

namespace App\Enums;

enum ItemStatus: string
{
    case AVAILABLE = 'available';
    case UNPUBLISHED = 'unpublished';
    case SOLD = 'sold';
}
