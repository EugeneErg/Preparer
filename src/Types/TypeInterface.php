<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

interface TypeInterface
{
    public function getAncestors(): FunctionCollection;
    public function getParent(): ?AbstractFunction;
    public function getChildren(): FunctionCollection;
}
