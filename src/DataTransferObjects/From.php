<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Types\BooleanType;

final class From
{
    public function __construct(
        public readonly Query $query,
        public readonly JoinTypeEnum $joinType = JoinTypeEnum::Outer,
        public readonly ?BooleanType $on = null,
    ) {
    }
}
