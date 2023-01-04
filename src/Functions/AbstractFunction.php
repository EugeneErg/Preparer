<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions;

use EugeneErg\Preparer\Types\AbstractType;

abstract class AbstractFunction
{
    protected const RETURN_TYPE = null;
    public readonly AbstractType $context;

    /** @return class-string<AbstractType> */
    protected function getType(): string
    {
        return static::RETURN_TYPE ?? get_class($this->context);
    }

    public function __invoke(): AbstractType
    {
        $class = $this->getType();

        return new $class($this);
    }

    public function equals(self $function): bool
    {
        return get_class($function) === static::class && $function->context === $this->context;
    }
}
