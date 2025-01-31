<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case VENDOR = 'vendor';
    case CUSTOMER = 'customer';
}
