<?php

namespace App\Enum;

enum VerificationType : string
{
    case User = "user";
    case Company = "company";
    case Address = "address";
}
