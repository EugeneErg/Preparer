<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\FunctionCollection;

interface TypeInterface
{
    public function getMethods(): FunctionCollection;
    public function getChildMethods(): FunctionCollection;
}
