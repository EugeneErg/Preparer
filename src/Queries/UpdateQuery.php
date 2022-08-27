<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Functions\Query\OrderBy;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use JetBrains\PhpStorm\Pure;

class UpdateQuery extends AbstractQuery
{
    #[Pure] public function __construct(
        public readonly Table $table,
        public readonly Returning $source,
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
        parent::__construct();
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        return $this->call(new OrderBy($value, $desc));
    }
}
