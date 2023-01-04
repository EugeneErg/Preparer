<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Enums;

use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\Data\Union;
use EugeneErg\Preparer\Data\Value;
use EugeneErg\Preparer\Data\Values;
use EugeneErg\Preparer\Queries\DeleteQuery;
use EugeneErg\Preparer\Queries\InsertQuery;
use EugeneErg\Preparer\Queries\SelectQuery;
use EugeneErg\Preparer\Queries\UpdateQuery;

enum QueryTypeEnum: string
{
    case Select = SelectQuery::class;
    case Delete = DeleteQuery::class;
    case Update = UpdateQuery::class;
    case Insert = InsertQuery::class;
    case Union = Union::class;
    case Table = Table::class;
    case Values = Values::class;
    case Value = Value::class;
}
