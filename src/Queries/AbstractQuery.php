<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\QueryTypeCollection;
use EugeneErg\Preparer\Collections\QueryTypeCollectionInterface;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Functions\Query\Where;
use EugeneErg\Preparer\Types\AbstractType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\CountableTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;

abstract class AbstractQuery extends AbstractType implements CountableTypeInterface, QueryTypeInterface
{
    public function __construct(private readonly QueryTypeEnum $type)
    {
        parent::__construct();
    }

    public function getType(): QueryTypeEnum
    {
        return $this->type;
    }

    public function where(BooleanType $value): self
    {
        parent::call(new Where($this, $value));

        return $this;
    }

    public function __toString(): string
    {
        return spl_object_hash($this);
    }

    public function getChildren(): QueryTypeCollectionInterface
    {
        return QueryTypeCollection::fromMap(
            true,
            fn (From $from): QueryTypeInterface => $from->source,
            $this->getChildMethods()
                ->filter(fn (AbstractFunction $function): bool => $function instanceof From),
        );
    }
}
