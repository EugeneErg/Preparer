<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

enum RoundTypeEnum: string
{
    case Down = 'down';
    case Up = 'up';
    case Nearest = 'nearest';
}
