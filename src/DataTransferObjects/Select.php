<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Collections\BranchCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

final class Select
{
    public function __construct(
        public readonly BranchCollection $path,
        public readonly AbstractFunction $method,
    ) {
    }
}



