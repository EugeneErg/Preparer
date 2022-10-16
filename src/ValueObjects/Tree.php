<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\ValueObjects;

use EugeneErg\Preparer\Collections\TreeCollection;
use EugeneErg\Preparer\Types\QueryTypeInterface;

final class Tree
{
    public readonly TreeCollection $children;
    public readonly int $level;

    public function __construct(
        public readonly QueryTypeInterface $query,
        public readonly ?Tree $parent = null,
    ) {
        $this->level = ($parent?->level ?? 0) + 1;
    }

    public function getPath(self $from): TreeCollection
    {
        $to = $this;
        $result = [];

        while ($from !== $to) {
            if ($from->level >= $to->level) {
                $result[] = $from;
                $from = $from->parent;
            }

            if ($to->level > $from->level) {
                $to = $to->parent;
            }
        }

        return new TreeCollection($result);
    }
}
