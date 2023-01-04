<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Enums\QueryTypeEnum;

class PreparerValues extends AbstractData
{
    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Values;
    }
}
