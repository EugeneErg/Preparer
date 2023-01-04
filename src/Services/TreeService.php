<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Services;

use EugeneErg\Preparer\Collections\TreeCollection;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use EugeneErg\Preparer\ValueObjects\Tree;

final class TreeService
{
    public function getQueryStructure(QueryTypeInterface $query): TreeCollection
    {
        $result = new TreeCollection([], false);
        $this->createTree($query, $result);

        return $result->setImmutable();
    }

    private function createTree(QueryTypeInterface $query, TreeCollection $trees, Tree $parent = null): Tree
    {
        $hash = spl_object_hash($query);

        if (isset($trees[$hash])) {
            throw new \LogicException('The same subquery cannot be reused in the same query.');
        }

        $trees[$hash] = $result = new Tree($query, $parent);
        $result->children = TreeCollection::fromMap(
            true,
            fn (QueryTypeInterface $source): Tree => $this->createTree($source, $trees, $result),
            $query->getChildren(),
        );

        return $result;
    }
}
