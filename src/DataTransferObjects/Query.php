<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Collections\FromCollection;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class Query
{
    public function __construct(
        public readonly QueryTypeEnum $type,
        public readonly JoinTypeEnum $join,
        public readonly TypeCollection $returning,
        public readonly QueryTypeInterface $source,
        public readonly ?TypeCollection $action = null,
        public readonly ?BooleanType $where = null,
        public readonly ?BooleanType $having = null,
        public readonly ?BooleanType $on = null,
        public readonly ?TypeCollection $groupBy = null,
        public readonly ?FunctionCollection $orderBy = null,
        public readonly ?FromCollection $subQueries = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = 0,
        public readonly ?bool $distinct = false,
    ) {
    }
}
