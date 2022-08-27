<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

interface FieldTypeInterface extends CountableTypeInterface
{
    public function isNull(): BooleanType;
}
