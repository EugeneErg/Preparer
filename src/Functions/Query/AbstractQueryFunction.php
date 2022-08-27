<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;
use JetBrains\PhpStorm\Pure;

abstract class AbstractQueryFunction extends AbstractFunction
{
    /** @return QueryTypeInterface */
    #[Pure] public function __invoke(TypeInterface $type): TypeInterface
    {
        return $type;
    }
}
