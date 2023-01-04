<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

interface TypeInterface
{
    public function getFunctionThatReturnsThisValue(): ?AbstractFunction;
    public function getResults(): TypeCollection;
}
