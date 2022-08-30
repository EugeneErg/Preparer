<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Data\Table;

class InsertQuery extends AbstractQuery
{
    public readonly TypeCollection $action;

    public function __construct(
        public readonly Table $table,
        Returning $source,
    ) {
        $this->call(new From($source->source));
        $this->action = $source->select;
        parent::__construct(QueryTypeEnum::Insert);
    }
}
