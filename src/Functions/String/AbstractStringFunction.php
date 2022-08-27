<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\String;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\StringType;

/**
 * @method StringType __invoke(StringType $type)
 */
class AbstractStringFunction extends AbstractFunction
{
    protected const RETURN_TYPE = StringType::class;
}
