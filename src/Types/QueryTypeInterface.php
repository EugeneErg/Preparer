<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\QueryTypeCollectionInterface;
use EugeneErg\Preparer\Enums\QueryTypeEnum;

interface QueryTypeInterface extends TypeInterface
{
    public function __toString(): string;
    public function getType(): QueryTypeEnum;
    public function getSubQueries(): QueryTypeCollectionInterface;
}
