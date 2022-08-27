<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Angle;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\AngleType;
use EugeneErg\Preparer\Types\TypeInterface;

/**
 * @method AngleType __invoke(TypeInterface $type)
 */
abstract class AbstractAngleFunction extends AbstractFunction
{
    protected const RETURN_TYPE = AngleType::class;
}
