<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use EugeneErg\Preparer\Returning;
use JetBrains\PhpStorm\Pure;

class Union extends AbstractData
{
    /** @var Returning[] */
    public readonly array $sources;

    #[Pure] public function __construct(public readonly bool $distinct = false, Returning ...$sources)
    {
        $this->sources = $sources;
        parent::__construct();
    }
}
