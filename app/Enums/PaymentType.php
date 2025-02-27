<?php

namespace App\Enums;

enum PaymentType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
    case EXCHANGE = 'exchange';
    case BILL = 'bill';
    case EXPENSE = 'expense';
}
