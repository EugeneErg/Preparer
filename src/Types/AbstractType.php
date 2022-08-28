<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

abstract class AbstractType implements TypeInterface
{
    private array $childMethods = [];
    private readonly FunctionCollection $methods;

    public function __construct(FunctionCollection $methods = null)
    {
        $this->methods = $methods ?? new FunctionCollection();
    }

    public function getMethods(): FunctionCollection
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
