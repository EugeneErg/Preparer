<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions;

use EugeneErg\Preparer\Types\TypeInterface;

abstract class AbstractFunction
{
    protected const RETURN_TYPE = null;

    /** @return TypeInterface */
    protected function getType(TypeInterface $type): string
    {
        return static::RETURN_TYPE ?? get_class($type);
    }

    public function __invoke(TypeInterface $type): TypeInterface
    {
        $class = $this->getType($type);

        return new $class($type->getMethods()->set($this));
    }

    public function equals(self $function): bool
    {
        return get_class($function) === static::class;
    }
}
