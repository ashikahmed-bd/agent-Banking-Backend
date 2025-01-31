<?php

namespace App\Enums;

enum PaymentType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
    case RECEIVED = 'received';
    case PAYABLE = 'payable';
}
