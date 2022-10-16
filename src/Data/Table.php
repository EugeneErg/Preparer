<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

class Table extends AbstractData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $schema = null,
        public readonly ?string $base = null,
    ) {
        parent::__construct();
    }
}
