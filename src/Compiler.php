<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\DataTransferObjects\Query;

final class Compiler
{
    public function __construct(
        private readonly Builder $builder,
        private readonly CompilerCollection $compilers,
    ) {
    }

    public function toString(Query $query, string $driver, ?string $version = null): string
    {
        if (!isset($this->compilers[$driver])) {
            throw new \Exception('Compiler not found', [$driver]);
        }


        //todo
    }
}
