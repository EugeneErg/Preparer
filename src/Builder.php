<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Collections\FromCollection;
use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Data\AbstractData;
use EugeneErg\Preparer\Data\Union;
use EugeneErg\Preparer\DataTransferObjects\Query;
use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Functions\Query\GroupBy;
use EugeneErg\Preparer\Functions\Query\Having;
use EugeneErg\Preparer\Functions\Query\OrderBy;
use EugeneErg\Preparer\Functions\Query\Where;
use EugeneErg\Preparer\Queries\AbstractQuery;
use EugeneErg\Preparer\Queries\DeleteQuery;
use EugeneErg\Preparer\Queries\InsertQuery;
use EugeneErg\Preparer\Queries\SelectQuery;
use EugeneErg\Preparer\Queries\UpdateQuery;

class Builder
{
    public function build(Returning $returning): Query
    {
        return $this->buildWithJointType($returning);
    }

    private function buildWithJointType(Returning $returning, JoinTypeEnum $joinType = JoinTypeEnum::Outer): Query
    {
        if ($returning->source instanceof Union) {
            return $this->buildFromUnion($returning->source, $returning->select, $joinType);
        }

        if ($returning->source instanceof AbstractData) {
            return $this->buildFromData($returning->source, $returning->select, $joinType);
        }

        return $this->buildFromQuery($returning->source, $returning->select, $joinType);
    }

    public function buildQuery(AbstractQuery $query): Query
    {
        return $this->build(new Returning(new TypeCollection(), $query));
    }

    private function buildFromQuery(
        AbstractQuery $query,
        TypeCollection $select,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ): Query {
        $functions = $query->getChildMethods()->reduce(
            function (array $init, AbstractFunction $function): array {
                $key = match (get_class($function)) {
                    Where::class => 'where',
                    Having::class => 'having',
                    GroupBy::class => 'group_by',
                    OrderBy::class => 'order_by',
                    From::class => 'from',
                };
                $init[$key][] = $function;

                return $init;
            },
            [],
        );

        foreach ($functions as $name => $function) {
            $functions[$name] = new FunctionCollection($function);
        }

        if ($query instanceof SelectQuery) {
            $from = $functions['from'];
            $type = QueryTypeEnum::Select;
            $limit = $query->limit;
            $offset = $query->offset;
            $distinct = $query->distinct;
        } elseif ($query instanceof DeleteQuery) {
            $from = $functions['from'];
            $type = QueryTypeEnum::Delete;
            $limit = $query->limit;
            $offset = $query->offset;
        } elseif ($query instanceof UpdateQuery) {
            $from = [new From($query->source->source)];
            $action = $query->source;
            $type = QueryTypeEnum::Update;
            $limit = $query->limit;
            $offset = $query->offset;
        } elseif ($query instanceof InsertQuery) {
            $from = [new From($query->source->source)];
            $action = $query->source;
            $type = QueryTypeEnum::Insert;
        }

        $subQueries = [];

        /** @var From $fromFunction */
        foreach ($from as $fromFunction) {
            $subQueries[] = $this->buildWithJointType($fromFunction->source, $fromFunction->joinType);
        }

        return new Query(
            $type,
            $joinType,
            $select,
            $query,
            $action ?? null,
            $functions['where'] ?? null,
            $functions['having'] ?? null,
            $functions['group_by'] ?? null,
            $functions['order_by'] ?? null,
            new FromCollection($subQueries),
            $limit ?? null,
            $offset ?? 0,
            $distinct ?? false,
        );
    }

    private function buildFromData(
        AbstractData $query,
        TypeCollection $select,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ): Query {
        return new Query(
            QueryTypeEnum::Select,
            $joinType,
            $select,
            $query,
        );
    }

    private function buildFromUnion(
        Union $query,
        TypeCollection $select,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ): Query {
        $subQueries = [];

        foreach ($query->sources as $source) {
            $subQueries[] = $this->build($source);
        }

        return new Query(
            QueryTypeEnum::Union,
            $joinType,
            $select,
            $query,
            subQueries: new FromCollection($subQueries),
            distinct: $query->distinct,
        );
    }
}
