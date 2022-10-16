<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Collections\TreeCollection;
use EugeneErg\Preparer\Types\FieldTypeInterface;

final class Select
{
    public function __construct(
        public readonly TreeCollection $path,
        public readonly FieldTypeInterface $field,
    ) {
    }
}
