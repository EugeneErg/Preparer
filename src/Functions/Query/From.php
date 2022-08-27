<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class From extends AbstractQueryFunction
{
    public function __construct(
        public readonly QueryTypeInterface $source,
        public readonly JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ) {
    }
}
