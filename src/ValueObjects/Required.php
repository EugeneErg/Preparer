<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\ValueObjects;

use EugeneErg\Preparer\Collections\BranchCollection;
use EugeneErg\Preparer\Collections\DestinationCollection;
use EugeneErg\Preparer\Collections\RequiredCollection;
use EugeneErg\Preparer\Collections\SelectCollection;
use EugeneErg\Preparer\DataTransferObjects\Destination;
use EugeneErg\Preparer\DataTransferObjects\Select;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Returning;
use EugeneErg\Preparer\Types\AbstractType;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use LogicException;

final class Required
{
    public function __construct(
        public readonly AbstractFunction $target,//данная функция
        public readonly BranchCollection $executionRange,//диапазон в рамках которого могла быть выполнена функция
        public readonly SelectCollection $used,//функции, в которых участвует данная, для того, чтобы удалить её из селектов, если она перестанет быть нужна
        public readonly DestinationCollection $destinations,//то, куда надо извлекать с определенными альясами
    ) {
    }

    public static function getStructure(Returning $returning, BranchCollection $structure): RequiredCollection
    {
        $result = new RequiredCollection([], false);

        foreach ($returning->select as $alias => $item) {
            self::fillWithSelect($item, $returning->source, $structure, $result, (string) $alias);
        }

        foreach ($returning->source->getChildren() as $method) {
            self::fillWithoutSelect($method, $returning->source, $structure, $result);
        }

        return $result->setImmutable();
    }

    private static function fillWithSelect(
        FieldTypeInterface $item,
        QueryTypeInterface $source,
        BranchCollection $structure,
        RequiredCollection $requires,
        string $alias,
    ): void {
        $method = $item->getParent();
        $destination = $structure[spl_object_hash($source)];
        $executionRange = self::getSourceBranches($destination, $method, $structure, $requires);
        $hash = spl_object_hash($method);
        $requires[$hash] = self::merge(new self(
            $method,
            $executionRange->setImmutable(),
            new SelectCollection(),
            new DestinationCollection([new Destination($alias, $destination, new BranchCollection())]),
        ), $requires[$hash] ?? null, $requires);
    }

    private static function fillWithoutSelect(
        AbstractFunction $method,
        QueryTypeInterface $source,
        BranchCollection $structure,
        RequiredCollection $requires,
        ?AbstractFunction $used = null,
    ): BranchCollection {
        $destination = $structure[spl_object_hash($source)];
        $hash = spl_object_hash($method);

        if ($method->context instanceof QueryTypeInterface && $destination->query !== $method->context) {
            $executionRange = $destination->getPath($structure[spl_object_hash($method->context)])
                ->setImmutable(false);
            $select = $executionRange->splice(1);
        } else {
            $executionRange = self::getSourceBranches($destination, $method, $structure, $requires);
            $select = new BranchCollection();
        }

        $requires[$hash] = self::merge(new self(
            $method,
            $executionRange->setImmutable(),
            new SelectCollection($used === null ? [] : [new Select($select, $used)]),
            new DestinationCollection($used === null ? [new Destination(null, $destination, $select)] : []),
        ), $requires[$hash] ?? null, $requires);

        return $executionRange;
    }

    public static function getSourceBranches(
        Branch $destination,
        AbstractFunction $method,
        BranchCollection $structure,
        RequiredCollection $requires,
    ): BranchCollection {
        /** Все источники, которые нужны для этой функции */
        $allSources = self::getAllSources(get_object_vars($method), $destination, $structure, $requires, $method)->unique();
        $paths = [];

        foreach ($allSources as $source) {
            $paths[] = $destination->getPath($source);
        }

        return BranchCollection::fromIntersect(false, true, false, ...$paths)
            ->set($destination)->setImmutable();
    }

    private static function getAllSources(
        iterable $values,
        Branch $destination,
        BranchCollection $structure,
        RequiredCollection $requires,
        ?AbstractFunction $method,
    ): BranchCollection {
        $result = [];

        foreach ($values as $value) {
            if ($value instanceof QueryTypeInterface && $value !== $destination->query) {
                throw new LogicException('There are no aggregate functions across multiple queries.');
            }

            if (is_iterable($value)) {//массивы данных
                /** @var iterable $value */
                $result[] = self::getAllSources($value, $destination, $structure, $requires, $method);
            } elseif (
                !$value instanceof QueryTypeInterface
                && $value instanceof AbstractType
                && $value->getParent() !== null
            ) {//результаты выполнения функций или чистые данные
                $result[] = self::fillWithoutSelect($value->getParent(), $destination->query, $structure, $requires, $method);
            }
        }

        return BranchCollection::fromMerge(false, ...$result);
    }

    private static function merge(self $requiredA, ?self $requiredB, RequiredCollection $requires): self
    {
        if ($requiredB === null) {
            return $requiredA;
        }

        $pathA = BranchCollection::fromDiff(true, true, false, $requiredA->executionRange, $requiredB->executionRange);
        $pathB = BranchCollection::fromDiff(true, true, false, $requiredB->executionRange, $requiredA->executionRange);
        $result = new self(
            $requiredA->target,
            BranchCollection::fromIntersect(
                true,
                true,
                false,
                $requiredA->executionRange,
                $requiredB->executionRange,
            ),
            SelectCollection::fromMerge(
                true,
                SelectCollection::fromMap(true, function (Select $select) use ($pathA): select {
                    return new Select(BranchCollection::fromMerge(true, $pathA, $select->path), $select->method);
                }, $requiredA->used),
                SelectCollection::fromMap(true, function (Select $select) use ($pathB): select {
                    return new Select(BranchCollection::fromMerge(true, $pathB, $select->path), $select->method);
                }, $requiredB->used),
            ),
            DestinationCollection::fromMerge(true, $requiredA->destinations, $requiredB->destinations),
        );
        self::restrictChildrenExecutionRange($result->target, $result->executionRange->last(), $requires);

        return $result;
    }

    private static function restrictChildrenExecutionRange(
        AbstractFunction $target,
        Branch $upperRestrict,
        RequiredCollection $requires,
    ): void {
        foreach (get_object_vars($target) as $value) {
            if (
                !$value instanceof QueryTypeInterface
                && $value instanceof AbstractType
                && $value->getParent() !== null
            ) {
                self::restrictExecutionRange($value->getParent(), $upperRestrict, $requires);
            }
        }
    }

    private static function restrictExecutionRange(
        AbstractFunction $method,
        Branch $upperRestrict,
        RequiredCollection $requires,
    ): void {
        $hash = spl_object_hash($method);
        $result = $requires[$hash];
        $maxPos = self::getPosition($result->executionRange, $upperRestrict);
        $requires[$hash] = isset($maxPos)
            ? new self(
                $result->target,
                $result->executionRange->slice(0, $maxPos + 1),
                new SelectCollection(),
                $result->destinations,
            )
            : new self(//могут образоваться селекты с одинаоквыми путями
                $result->target,
                $result->executionRange,
                SelectCollection::fromMap(true, function (Select $select) use ($upperRestrict): Select {
                    $maxPos = self::getPosition($select->path, $upperRestrict);

                    return $maxPos === null
                        ? $select
                        : new Select($select->path->slice(0, $maxPos + 1), $select->method);
                }, $result->used),
                $result->destinations,
            );
        self::restrictChildrenExecutionRange($requires[$hash]->target, $upperRestrict, $requires);
    }

    private static function getPosition(BranchCollection $collection, Branch $search): ?int
    {
        $pos = 0;

        foreach ($collection as $value) {
            if ($value === $search) {
                return $pos;
            }

            $pos++;
        }

        return null;
    }
}
