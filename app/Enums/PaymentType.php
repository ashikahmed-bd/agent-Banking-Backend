<?php

namespace App\Enums;

enum PaymentType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case TRANSFER = 'transfer';
    case BILL = 'bill';
}
