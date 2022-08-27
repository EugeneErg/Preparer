<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use JetBrains\PhpStorm\Pure;

class Values extends AbstractData
{
    /** @var Value[] */
    public readonly array $values;

    #[Pure] public function __construct(Value ...$values)
    {
        $this->values = $values;
        parent::__construct();
    }
}
