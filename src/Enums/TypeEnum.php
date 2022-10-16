<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

enum TypeEnum: string
{
    case Numeric = 'numeric';
    case Integer = 'integer';
    case Decimal = 'decimal';
    case Degrees = 'degrees';
    case Radians = 'radians';
    case String = 'string';
    case Boolean = 'boolean';
    case Object = 'object';
}
