<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

class Value extends PreparerValue
{
    public function __construct(public readonly array $data)
    {
        parent::__construct();
    }
}
