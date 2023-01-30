<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\ValueObjects;

use EugeneErg\Preparer\Collections\BranchCollection;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use JetBrains\PhpStorm\ArrayShape;

final class Branch
{
    public readonly BranchCollection $children;
    public readonly int $level;

    public function __construct(
        public readonly QueryTypeInterface $query,
        public readonly ?Branch $parent = null,
    ) {
        $this->level = ($parent?->level ?? 0) + 1;
    }

    public function getPath(self $from): BranchCollection
    {
        $to = $this;
        $result = new BranchCollection([], false);

        while ($from !== $to) {
            if ($from->level >= $to->level) {
                $result[] = $from;
                $from = $from->parent;
            }

            if ($to->level > $from->level) {
                $to = $to->parent;
            }
        }

        return $result->setImmutable();
    }

    public static function getStructure(QueryTypeInterface $query): BranchCollection
    {
        $result = new BranchCollection([], false);
        self::createTree($query, $result);

        return $result->setImmutable();
    }

    private static function createTree(QueryTypeInterface $query, BranchCollection $trees, Branch $parent = null): Branch
    {
        $hash = spl_object_hash($query);

        if (isset($trees[$hash])) {
            throw new \LogicException('The same subquery cannot be reused in the same query.');
        }

        $trees[$hash] = $result = new Branch($query, $parent);
        $result->children = BranchCollection::fromMap(
            true,
            fn (QueryTypeInterface $source): Branch => self::createTree($source, $trees, $result),
            $query->getSubQueries(),
        );

        return $result;
    }

    #[ArrayShape([
        'level' => "int",
        'parent' => Branch::class | null,
        'query' => QueryTypeInterface::class,
        'children' => BranchCollection::class,
    ])]
    public function __debugInfo(): array
    {
        return [
            'level' => $this->level,
            'parent' => $this->parent,
            'query' => $this->query,
            'children' => $this->children,
        ];
    }
}
