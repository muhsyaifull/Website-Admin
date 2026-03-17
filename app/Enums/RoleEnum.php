<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Educator = 'educator';
    case Cashier = 'cashier';
}