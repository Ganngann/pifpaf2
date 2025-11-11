<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case INITIATED = 'initiated';
    case PAYMENT_RECEIVED = 'payment_received';
    case DISPUTED = 'disputed';
    case COMPLETED = 'completed';
    case PICKUP_COMPLETED = 'pickup_completed';
    case SHIPPED = 'shipped';
    case IN_TRANSIT = 'in_transit';
    case DELIVERED = 'delivered';
    case REFUNDED = 'refunded';
}
