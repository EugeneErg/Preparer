<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\QueryTypeCollection;
use EugeneErg\Preparer\Collections\QueryTypeCollectionInterface;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Functions\Query\Where;
use EugeneErg\Preparer\Types\AbstractType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\CountableTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;

abstract class AbstractQuery extends AbstractType implements CountableTypeInterface, QueryTypeInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    public function where(BooleanType $value): self
    {
        $this->call(new Where($value));

        return $this;
    }

    public function __toString(): string
    {
        return spl_object_hash($this);
    }

    public function getSubQueries(): QueryTypeCollectionInterface
    {
        return QueryTypeCollection::fromMap(
            true,
            fn (From $result): QueryTypeInterface => $result->source,
            $this->getChildren()
                ->filter(fn (AbstractFunction $result): bool => $result instanceof From),
        );
    }
}
