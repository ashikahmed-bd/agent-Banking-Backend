<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
}
