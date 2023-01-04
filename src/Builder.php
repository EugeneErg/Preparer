<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Collections\FromCollection;
use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Collections\MutableReturningCollection;
use EugeneErg\Preparer\Collections\ReturningCollection;
use EugeneErg\Preparer\Collections\SelectCollection;
use EugeneErg\Preparer\Collections\TreeCollection;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Data\AbstractData;
use EugeneErg\Preparer\Data\Union;
use EugeneErg\Preparer\Data\Value;
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
use EugeneErg\Preparer\Services\TreeService;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use EugeneErg\Preparer\DataTransferObjects\From as FromDto;
use EugeneErg\Preparer\ValueObjects\Tree;

class Builder
{
    private const MAP = [
        Where::class => 'where',
        Having::class => 'having',
        GroupBy::class => 'group_by',
        OrderBy::class => 'order_by',
        From::class => 'from',
    ];

    public function __construct(private readonly TreeService $treeService)
    {
    }

    /** Выполнит запрос и вернет необходимые данные */
    public function build(Returning $returning): Query
    {
        $structure = $this->treeService->getQueryStructure($returning->source);
        $newReturning = $this->createReturning($returning, $structure);

        [$result, $returning] = $this->buildWithJointType(
            $returning->source,
            $returning->select,
            $structure,
            new MutableReturningCollection(),
        );

        return $result;
    }

    /** Выполнит запрос, не возвращая никаких данных */
    public function buildQuery(AbstractQuery $query): Query
    {
        return $this->build(new Returning(new TypeCollection(), $query));
    }

    private function createReturning(
        Returning $returning,
        TreeCollection $structure,
    ): ReturningCollection {//то, что в итоге извлекаем из всех источников
        $hash = spl_object_hash($returning->source);
        $result = [$hash => $returning];

        foreach ($returning->select as $alias => $item) {
            $this->distinguish($item, $returning->source, $structure);

        }
    }

    private function buildWithJointType(
        QueryTypeInterface $source,
        TypeCollection     $select,
        TreeCollection     $structure,
        array              $returning,
        array              $parents = [],
    ): array {
        if ($source instanceof AbstractData) {
            return $this->buildFromData($source, $select, $structure, $returning, $parents);
        }

        /** @var AbstractQuery $source */
        return $this->buildFromQuery($source, $select, $structure, $returning, $parents);
    }

    private function buildFromQuery(
        AbstractQuery  $query,
        TypeCollection $select,
        TreeCollection $structure,
        array $returning,
        array $parents = [],
    ): array {
        $hash = spl_object_hash($query);
        $parents[] = $hash;

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
            $returning = $this->getSelectFromFunctions($subFunction, $parents, $returning);
        }

        $returning = $this->getSelectFromIterable($select, $parents, $returning);
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
                    [$subQuery, $returning] = $this->buildWithJointType(
                        $fromFunction->source,
                        $returning[$hash]->select ?? new TypeCollection(),
                        $returning,
                        $parents,
                    );
                    $subQueries[] = new FromDto(
                        $subQuery,
                        $fromFunction->joinType,
                        $fromFunction->on,
                    );
                }
            }
        }

        return [new Query(
            $type,
            $select,
            $query,
            $action ?? null,
            $this->createConditions($functions['where'] ?? null),
            $this->createConditions($functions['having'] ?? null),
            $this->functionsToTypes($functions['group_by'] ?? null),
            $functions['order_by'] ?? null,
            new FromCollection($subQueries),
            $limit,
            $offset,
            $distinct,
        ), $returning];
    }

    private function buildFromData(
        AbstractData        $query,
        TypeCollection      $select,
        TreeCollection      $structure,
        ReturningCollection $returning,
        JoinTypeEnum        $joinType = JoinTypeEnum::Outer,
        BooleanType         $on = null,
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
        Union               $query,
        TypeCollection      $select,
        ReturningCollection $returning,
        JoinTypeEnum        $joinType = JoinTypeEnum::Outer,
        BooleanType         $on = null,
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
        FunctionCollection  $functions,
        QueryTypeInterface  $query,
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
        array|\Traversable  $items,
        QueryTypeInterface  $query,
        ReturningCollection $subSelect,
    ): ReturningCollection {
        foreach ($items as $item) {
            if ($item instanceof FieldTypeInterface) {
                $subSelect = $this->getSelectFromValue($item, $query, $subSelect);
            } elseif ($item instanceof AbstractImmutableCollection || is_array($item)) {
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

    private function distinguish(
        FieldTypeInterface $item,
        QueryTypeInterface $source,
        TreeCollection $structure,
    ) {

        $context = $item->getMethods()->first()->context;
        $from = $context->value instanceof AbstractData
            ? $structure[spl_object_hash($context->value)]->parent?->query
            : $context->value;

        if ($from === null) {
            return;
        }

        if ($from === $source) {

        }

        foreach ($item->getMethods()->slice(1) as $method) {
            $this->getNearestSourceByMethod($method, $from, $source, $structure);

            if ($method instanceof AggregateFunction) {

            }


            foreach (get_object_vars($method) as $var) {

            }
        }

        if ($context->value !== $source) {
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

    private function getNearestSourceByMethod(
        Tree $to,
        AbstractFunction $data,
        TreeCollection $structure,
    ): SelectCollection {
        return $this->getNearestSourceByIterable($to, get_object_vars($data), $structure);
    }

    private function getNearestSourceByQuery(
        Tree $to,
        QueryTypeInterface $data,
        TreeCollection $structure,
    ): TreeCollection {
        if (
            !isset($structure[$data]->parent)
            && !$data instanceof Value
            && !$data instanceof Union
            && $data instanceof AbstractData
        ) {
            throw new \LogicException('The data must be included in the query.');
        }

        $from = ($data instanceof Union || !$data instanceof AbstractData
            ? $data : $structure[$data]->parent?->query) ?? $to->query;
        $toQuery = $to;
        $fromQuery = $structure[$from];

        return $toQuery->getPath($fromQuery);
    }

    private function getNearestSourceByIterable(
        Tree $to,
        iterable $data,
        TreeCollection $structure,
    ): TreeCollection {
        $from = $to;
        $result = [];

        foreach ($data as $datum) {
            if ($datum instanceof QueryTypeInterface) {
                $selectPath = $this->getNearestSourceByQuery($to, $datum, $structure);
            } elseif ($datum instanceof FieldTypeInterface) {
                $selectPath = $this->getNearestSourceByField($to, $datum, $structure);
            } elseif (is_iterable($datum)) {
                $selectPath = $this->getNearestSourceByIterable($to, $datum, $structure);
            } else {
                $selectPath = new TreeCollection();
            }

            if ($subResult !== $from) {


                $from = null;
            }
        }

        return $from;
    }

    private function getNearestSourceByField(
        Tree $to,
        FieldTypeInterface $data,
        TreeCollection $structure,
    ): SelectCollection {
        /**
         * 1) Существует дерево запросов
         * 2) Каждый запрос содержит значения
         * 3) Эти значения должны быть переданы другому запросу
         *
         * необходимо составить матрицу зависимостей запросов
         *
         * пример:
         *           q1 - родительский запрос
         *         /   \
         *        q2   q3[a2] - дочерние запросы
         *     /     \
         *    q4[a1]  q5 - дочерние запросы второго уровня
         *
         * пусть существует a1(q4, a2), необходимый для запроса q1,
         * где a1 это резулььтат выполнения функции в контексте q4
         * а a2(q3) это поле таблицы q3
         *
         * тогда
         * q1(a1)
         * a1(q4, a2)
         * a2(q3)
         *
         * необходимо вычислить где собираются какие части завсимостей
         *
         * итоговая матрица:
         *         q1
         *        /a1\a2
         *       q2  q3
         *       |a1
         *       q4
         * из этой матрицы виден порядок получения аргументов для q3
         * но необходимо дополнить матрицу реалными связями
         *
         *
         */
        $result = new TreeCollection();
        $path = $data instanceof QueryTypeInterface
            ? $this->getNearestSourceByQuery($to, $data, $structure)
            : new TreeCollection();
        $isParentContext = $path->isEmpty() ? null : $path->last()->level <= $to->level;

        foreach ($data->getMethods()->reverse() as $method) {
            $selectPath = $this->getNearestSourceByMethod($to, $method, $structure);

            if (!$selectPath->isEmpty()) {
                $lastElem = $selectPath->last();

                if ($lastElem->level > $to->level) {
                    if ($isParentContext) {
                        //$toContext <= f1(f2($fromContext.f3(a1), a2), a3)
                    }
                    //is child
                } else {
                    //is neigh
                }
            }
        }
    }
}
