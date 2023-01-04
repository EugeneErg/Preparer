<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Enums\QueryTypeEnum;

class PreparerValue extends AbstractData
{
    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Value;
    }
}
