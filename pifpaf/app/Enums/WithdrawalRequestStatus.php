<?php

namespace App\Enums;

enum WithdrawalRequestStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PAID = 'paid';
    case REJECTED = 'rejected';
    case FAILED = 'failed';
}
