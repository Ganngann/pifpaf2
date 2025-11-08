<?php

namespace App\Enums;

enum AddressType: string
{
    case PICKUP = 'pickup';
    case DELIVERY = 'delivery';
}
