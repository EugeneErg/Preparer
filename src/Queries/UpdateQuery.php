<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Functions\Query\OrderBy;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\Types\FieldTypeInterface;

class UpdateQuery extends AbstractQuery
{
    public readonly TypeCollection $action;

    public function __construct(
        public readonly Table $table,
        Returning $source,
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
        $this->call(new From($source->source));
        $this->action = $source->select;
        parent::__construct(QueryTypeEnum::Update);
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        return $this->call(new OrderBy($value, $desc));
    }
}
