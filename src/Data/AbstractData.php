<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Collections\QueryTypeCollection;
use EugeneErg\Preparer\Collections\QueryTypeCollectionInterface;
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
        /** @var FieldTypeInterface $result */
        $result = $this->call(new GetField($this->fields[$field], $field));

        return $result;
    }

    public function __toString(): string
    {
        return spl_object_hash($this);
    }

    public function getSubQueries(): QueryTypeCollectionInterface
    {
        return new QueryTypeCollection();
    }
}
