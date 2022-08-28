<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class Select
{
    public function __construct(public readonly QueryTypeInterface $context, public readonly TypeCollection $fields)
    {
    }
}
