<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

class Values extends AbstractData
{
    /** @var Value[] */
    public readonly array $values;

    public function __construct(Value ...$values)
    {
        $this->values = $values;
        parent::__construct();
    }
}
