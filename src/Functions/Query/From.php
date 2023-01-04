<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

class From extends AbstractQueryFunction
{
    public function __construct(
        TypeInterface $context,
        public readonly QueryTypeInterface $source,
        public readonly ?BooleanType $on = null,
        public readonly JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ) {
        parent::__construct($context);
    }
}
