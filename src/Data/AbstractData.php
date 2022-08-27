<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Types\AbstractDataType;
use EugeneErg\Preparer\Types\QueryTypeInterface;

abstract class AbstractData extends AbstractDataType implements QueryTypeInterface
{
}
