<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Data\Table;
use JetBrains\PhpStorm\Pure;

class InsertQuery extends AbstractQuery
{
    #[Pure] public function __construct(
        public readonly Table $table,
        public readonly Returning $source,
    ) {
        parent::__construct();
    }
}
