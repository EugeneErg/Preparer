<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\AbstractType;

abstract class AbstractQueryFunction extends AbstractFunction
{
    public function __invoke(): AbstractType
    {
        return $this->context;
    }
}
