<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Queries\SelectQuery;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class Returning
{
    public readonly QueryTypeInterface $source;

    public function __construct(
        public readonly TypeCollection $select,
        QueryTypeInterface|null $source = null,
    ) {
        $this->source = $source ?? new SelectQuery();
    }
}
