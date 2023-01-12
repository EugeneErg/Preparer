<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\DataTransferObjects;

use EugeneErg\Preparer\Collections\BranchCollection;
use EugeneErg\Preparer\ValueObjects\Branch;

final class Destination
{
    public function __construct(
        public readonly ?string $alias,
        public readonly Branch $destination,
        public readonly BranchCollection $path,
    ) {
    }
}
