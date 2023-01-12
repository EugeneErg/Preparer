<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Types\AbstractDataType;

class PreparerValue extends AbstractDataType
{
    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Value;
    }
}
