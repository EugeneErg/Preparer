<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Functions\Query\Context;
use EugeneErg\Preparer\Types\AbstractDataType;
use EugeneErg\Preparer\Types\QueryTypeInterface;

abstract class AbstractData extends AbstractDataType implements QueryTypeInterface
{
    public function __construct(FunctionCollection $methods = null)
    {
        parent::__construct(new FunctionCollection([(new Context($this))($this)]));
    }
}
