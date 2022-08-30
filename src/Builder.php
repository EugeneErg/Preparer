<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Collections\AbstractCollection;
use EugeneErg\Preparer\Collections\FromCollection;
use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Collections\ReturningCollection;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Data\AbstractData;
use EugeneErg\Preparer\Data\Union;
use EugeneErg\Preparer\DataTransferObjects\Query;
use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Query\Context;
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
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class Builder
{
    private const MAP = [
        Where::class => 'where',
        Having::class => 'having',
        GroupBy::class => 'group_by',
        OrderBy::class => 'order_by',
        From::class => 'from',
    ];

    public function build(Returning $returning): Query
    {
        [$result, $returning] = $this->buildWithJointType(
            $returning->source,
            $returning->select,
            new ReturningCollection(),
        );

        return $result;
    }

    public function buildQuery(AbstractQuery $query): Query
    {
        [$result, $returning] = $this->buildFromQuery(
            $query,
            new TypeCollection(),
            new ReturningCollection(),
        );

        return $result;
    }

    private function buildWithJointType(
        QueryTypeInterface $source,
        TypeCollection $select,
        ReturningCollection $returning,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
        BooleanType $on = null,
    ): array {
        if ($source instanceof AbstractData) {
            return $this->buildFromData($source, $select, $returning, $joinType, $on);
        }

        /** @var AbstractQuery $source */
        return $this->buildFromQuery($source, $select, $returning, $joinType, $on);
    }

    private function buildFromQuery(
        AbstractQuery $query,
        TypeCollection $select,
        ReturningCollection $returning,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
        BooleanType $on = null,
    ): array {
        $hash = spl_object_hash($query);

        if (isset($returning[$hash])) {
            $select = TypeCollection::fromMerge($select, $returning[$hash]->select);
            $returning = $returning->unset($hash);
        }

        $functions = $query->getChildMethods()->reduce(
            function (array $init, AbstractFunction $function): array {
                $init[self::MAP[get_class($function)]][] = $function;

                return $init;
            },
            [],
        );

        foreach ($functions as $name => $function) {
            $functions[$name] = new FunctionCollection($function);
        }

        /** @var FunctionCollection[] $functions */
        $from = $functions['from'] ?? new FunctionCollection();
        $limit = $query->limit ?? null;
        $offset = $query->offset ?? 0;
        $distinct = $query->distinct ?? false;
        $action = $query->action ?? null;
        $type = $query->type;

        foreach ($functions as $subFunction) {
            $returning = $this->getSelectFromFunctions($subFunction, $query, $returning);
        }

        $returning = $this->getSelectFromIterable($select, $query, $returning);
        $subQueries = [];

        foreach ([true, false] as $isCorrelate) {
            /** @var From $fromFunction */
            foreach ($isCorrelate ? $from->reverse() : $from as $fromFunction) {
                if (
                    $isCorrelate === (
                        $fromFunction->joinType === JoinTypeEnum::Correlate
                        && $fromFunction instanceof AbstractQuery
                    )
                ) {
                    $hash = spl_object_hash($fromFunction->source);
                    [$subQueries[], $returning] = $this->buildWithJointType(
                        $fromFunction->source,
                        $returning[$hash]->select ?? new TypeCollection(),
                        $returning,
                        $fromFunction->joinType,
                        $fromFunction->on,
                    );
                }
            }
        }

        return [new Query(
            $type,
            $joinType,
            $select,
            $query,
            $action ?? null,
            $this->createConditions($functions['where'] ?? null),
            $this->createConditions($functions['having'] ?? null),
            $on,
            $this->functionsToTypes($functions['group_by'] ?? null),
            $functions['order_by'] ?? null,
            new FromCollection($subQueries),
            $limit,
            $offset,
            $distinct,
        ), $returning];
    }

    private function buildFromData(
        AbstractData $query,
        TypeCollection $select,
        ReturningCollection $returning,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
        BooleanType $on = null,
    ): array {
        if ($query instanceof Union) {
            return $this->buildFromUnion($query, $select, $returning, $joinType, $on);
        }

        $hash = spl_object_hash($query);

        if (isset($returning[$hash])) {
            $select = TypeCollection::fromMerge($select, $returning[$hash]->select);
            $returning->unset($hash);
        }

        return [new Query(
            QueryTypeEnum::Select,
            $joinType,
            $select,
            $query,
            on: $on,
        ), $returning];
    }

    private function buildFromUnion(
        Union $query,
        TypeCollection $select,
        ReturningCollection $returning,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
        BooleanType $on = null,
    ): array {
        $hash = spl_object_hash($query);

        if (isset($returning[$hash])) {
            $select = TypeCollection::fromMerge($select, $returning[$hash]->select);
            $returning = $returning->unset($hash);
        }

        $subReturning = $this->getSelectFromIterable($select, $query, $returning);
        $subQueries = [];

        foreach ($query->sources as $source) {
            [$subQueries[], $returning] = $this->buildWithJointType(
                $source->source,
                $source->select,
                $subReturning,
            );
        }

        return [new Query(
            QueryTypeEnum::Union,
            $joinType,
            $select,
            $query,
            on: $on,
            subQueries: new FromCollection($subQueries),
            distinct: $query->distinct,
        ), $returning];
    }

    private function getSelectFromValue(
        FieldTypeInterface $value,
        QueryTypeInterface $query,
        ReturningCollection $subSelect,
    ): ReturningCollection {
        $methods = $value->getMethods();
        /** @var Context $context */
        $context = $methods->first();

        if ($context->value !== $query) {
            $hash = spl_object_hash($context->value);

            return $subSelect->set(
                isset($subSelect[$hash])
                    ? $subSelect[$hash]->select->set($context->value)
                    : new Returning(new TypeCollection([$value]), $context->value),
                $hash,
            );
        }

        return $this->getSelectFromFunctions($methods, $query, $subSelect);
    }

    private function getSelectFromFunctions(
        FunctionCollection $functions,
        QueryTypeInterface $query,
        ReturningCollection $subSelect,
    ): ReturningCollection {
        foreach ($functions as $function) {
            $subSelect = $function instanceof From
                ? ($function->on === null ? $subSelect : $this->getSelectFromValue($function->on, $query, $subSelect))
                : $this->getSelectFromIterable(get_object_vars($function), $query, $subSelect);
        }

        return $subSelect;
    }

    private function getSelectFromIterable(
        array|\Traversable $items,
        QueryTypeInterface $query,
        ReturningCollection $subSelect,
    ): ReturningCollection {
        foreach ($items as $item) {
            if ($item instanceof FieldTypeInterface) {
                $subSelect = $this->getSelectFromValue($item, $query, $subSelect);
            } elseif ($item instanceof AbstractCollection || is_array($item)) {
                $subSelect = $this->getSelectFromIterable($item, $query, $subSelect);
            }
        }

        return $subSelect;
    }

    private function createConditions(?FunctionCollection $methods): ?BooleanType
    {
        return $methods?->reduce(fn (?BooleanType $result, Where|Having $method): BooleanType =>
            $result === null ? $method->value : $result->and($method->value),
        );
    }

    private function functionsToTypes(?FunctionCollection $methods): TypeCollection
    {
        return $methods?->reduce(fn (TypeCollection $result, GroupBy $method): TypeCollection =>
            TypeCollection::fromMerge($result, $method->values),
            new TypeCollection(),
        );
    }
}
