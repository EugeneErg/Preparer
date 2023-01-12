<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Collections\QueryTypeCollection;
use EugeneErg\Preparer\Collections\QueryTypeCollectionInterface;
use EugeneErg\Preparer\Collections\ReturningCollection;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class Union extends AbstractData
{
    public readonly ReturningCollection $sources;

    public function __construct(public readonly bool $distinct = false, Returning ...$sources)
    {
        parent::__construct();
        $this->sources = new ReturningCollection($sources);
    }

    public function getSubQueries(): QueryTypeCollectionInterface
    {
        return QueryTypeCollection::fromMap(
            true,
            fn (Returning $returning): QueryTypeInterface => $returning->source,
            $this->sources,
        );
    }

    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Union;
    }
}
