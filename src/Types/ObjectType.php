<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Functions\Boolean\IsNull;

class ObjectType extends AbstractDataType implements FieldTypeInterface
{
    public function isNull(): BooleanType
    {
        return $this->call(new IsNull());
    }
}
