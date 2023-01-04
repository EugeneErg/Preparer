<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Functions\Query\OrderBy;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;

/**
 * PostgresQl
 * DELETE FROM films USING producers
 * WHERE producer_id = producers.id AND producers.name = 'foo';
 *
 * MySql
 * DELETE
 * p1
 * FROM posts AS p1
 * CROSS JOIN (
 * SELECT ID FROM posts GROUP BY id HAVING COUNT(id) > 1
 * ) AS p2
 * USING (id)
 *
 */
class DeleteQuery extends AbstractQuery
{
    public function __construct(
        public readonly Table $table,
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
        parent::__construct();
    }

    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Delete;
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        /** @var self $result */
        $result = $this->call(new OrderBy($value, $desc));

        return $result;
    }

    public function from(
        QueryTypeInterface $source,
        BooleanType $on = null,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ): self {
        $this->call(new From($source, $on, $joinType));

        return $this;
    }
}
