<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions;

use EugeneErg\Preparer\Types\TypeInterface;

abstract class AbstractFunction
{
    protected const RETURN_TYPE = null;

    public function __construct(public readonly TypeInterface $context)
    {
    }

    /** @return class-string<TypeInterface> */
    protected function getType(): string
    {
        return static::RETURN_TYPE ?? get_class($this->context);
    }

    public function __invoke(): TypeInterface
    {
        $class = $this->getType();

        return new $class($this->context->getMethods()->set($this));
    }

    public function equals(self $function): bool
    {
        return get_class($function) === static::class && $function->context === $this->context;
    }
}
