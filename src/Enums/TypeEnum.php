<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

enum TypeEnum: string
{
    case Float = 'float';
    case Integer = 'integer';
    case Decimal = 'decimal';
    case FloatDegrees = 'float_degrees';
    case FloatRadians = 'float_radians';
    case IntegerDegrees = 'integer_degrees';
    case IntegerRadians = 'integer_radians';
    case DecimalDegrees = 'decimal_degrees';
    case DecimalRadians = 'decimal_radians';
    case String = 'string';
    case Boolean = 'boolean';
    case Object = 'object';
}
