<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

enum QueryTypeEnum: string
{
    case Select = 'select';
    case Delete = 'delete';
    case Update = 'update';
    case Insert = 'insert';
    case Union = 'union';
}
