<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use JetBrains\PhpStorm\Pure;

class Value extends AbstractData
{
    #[Pure] public function __construct(public readonly array $data)
    {
        parent::__construct();
    }
}
