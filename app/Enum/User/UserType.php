<?php

namespace App\Enum\User;

enum UserType : string
{
    case Client = 'client';
    case Designer = 'designer';
}
