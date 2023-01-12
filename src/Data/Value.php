<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

class Value extends PreparerValue
{
    public readonly array $data;

    public function __construct(mixed ...$data)
    {
        $this->data = $data;
        parent::__construct();
    }
}
