<?php

declare(strict_types=1);

namespace App\Enum;

enum StatutPublication: string
{
    case Brouillon = 'brouillon';
    case Publie = 'publie';
}
