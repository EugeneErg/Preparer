<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Enums\TypeEnum;
use EugeneErg\Preparer\Functions\Numeric\GetField;
use EugeneErg\Preparer\Types\AbstractDataType;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;

abstract class AbstractData extends AbstractDataType implements QueryTypeInterface
{
    /** @var TypeEnum[] */
    protected array $fields = [];

    public function __get(string $field): FieldTypeInterface
    {
        return $this->call(new GetField($this->fields[$field], $field));
    }

    public function __toString(): string
    {
        return spl_object_hash($this);
    }
}
