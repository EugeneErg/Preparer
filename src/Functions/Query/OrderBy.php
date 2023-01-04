<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

class OrderBy extends AbstractQueryFunction
{
    public function __construct(
        TypeInterface $context,
        public readonly FieldTypeInterface $value,
        public readonly bool $desc = false,
    ) {
        parent::__construct($context);
    }
}
