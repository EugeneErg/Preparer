<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

interface QueryTypeInterface extends TypeInterface
{
    public function __toString(): string;
}
