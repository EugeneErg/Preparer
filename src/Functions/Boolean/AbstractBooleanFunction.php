<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Boolean;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\TypeInterface;

/**
 * @method BooleanType __invoke(TypeInterface $type)
 */
class AbstractBooleanFunction extends AbstractFunction
{
    protected const RETURN_TYPE = BooleanType::class;
}
