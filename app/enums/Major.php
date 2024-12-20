<?php

namespace App\Enums;

enum Major: string
{
    case مدير = 'manager';
    case مشرف = 'supervisor';
    case موظف = 'employee';
    case مشغل = 'operator';
    case كاميرا = 'payload';
    case تسليح = 'munition';
    case ميكانيكي = 'mechanic';
    case إلكتروني = 'avionics';
}
