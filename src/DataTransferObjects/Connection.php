<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Collections\FieldCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

final class Connection
{
    public function __construct(
        public readonly AbstractFunction $value,
        public readonly FieldCollection $targets,
    ) {
    }
}
