<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

enum NumericTypeEnum: string
{
    case Integer = 'integer';
    case Float = 'float';
    case Decimal = 'decimal';
}
