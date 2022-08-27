<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Data;

use JetBrains\PhpStorm\Pure;

class Table extends AbstractData
{
    #[Pure] public function __construct(
        public readonly string $name,
        public readonly ?string $schema = null,
        public readonly ?string $base = null,
    ) {
        parent::__construct();
    }
}
