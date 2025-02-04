<?php

namespace App\Enums;

enum PaymentType: string
{
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}
