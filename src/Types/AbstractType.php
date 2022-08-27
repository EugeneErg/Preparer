<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

abstract class AbstractType implements TypeInterface
{
    private array $childMethods = [];

    public function __construct(private readonly array $methods = [])
    {
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    protected function call(AbstractFunction $function): TypeInterface
    {
        $class = get_class($function);

        foreach ($this->childMethods[$class] ?? [] as [$childFunction, $result]) {
            if ($function->equals($childFunction)) {
                return $result;
            }
        }

        $result = $function($this);
        $this->childMethods[$class] = [$function, $result];

        return $result;
    }

    public function getChildMethods(): FunctionCollection
    {
        return new FunctionCollection(array_column($this->childMethods, 0));
    }
}