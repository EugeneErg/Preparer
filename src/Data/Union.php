<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Collections\ReturningCollection;
use EugeneErg\Preparer\Returning;

class Union extends AbstractData
{
    public readonly ReturningCollection $sources;

    public function __construct(public readonly bool $distinct = false, Returning ...$sources)
    {
        $this->sources = new ReturningCollection($sources);
        parent::__construct();
    }
}
