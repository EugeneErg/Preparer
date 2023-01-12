<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Enums\QueryTypeEnum;

class Values extends AbstractData
{
    /** @var PreparerValue[] */
    public readonly array $values;

    public function __construct(PreparerValue ...$values)
    {
        $this->values = $values;
        parent::__construct();
    }

    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Values;
    }
}
