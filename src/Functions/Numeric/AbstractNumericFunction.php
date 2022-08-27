<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\TypeInterface;

/**
 * @method NumericType __invoke(TypeInterface $type)
 */
class AbstractNumericFunction extends AbstractFunction
{
    protected const RETURN_TYPE = NumericType::class;
}
