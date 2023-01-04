<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Services;

use EugeneErg\Preparer\Collections\TreeCollection;
use EugeneErg\Preparer\Data\Union;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Queries\AbstractQuery;
use EugeneErg\Preparer\Returning;
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
        $result = new Tree($query, $parent);
        $hash = spl_object_hash($result);

        if (isset($trees[$hash])) {
            throw new \LogicException('The same subquery cannot be reused in the same query.');
        }

        $trees[$hash] = $result;

        if ($query instanceof AbstractQuery) {
            $result->children = TreeCollection::fromMap(
                true,
                fn (From $from): Tree => $this->createTree($from->source, $trees, $result),
                $query->getChildMethods()
                    ->filter(fn (AbstractFunction $function): bool => $function instanceof From),
            );
        } elseif ($query instanceof Union) {
            $result->children = TreeCollection::fromMap(
                true,
                fn (Returning $returning): Tree => $this->createTree($returning->source, $trees, $result),
                $query->sources,
            );
        } else {
            $result->children = new TreeCollection();
        }

        return $result;
    }
}
