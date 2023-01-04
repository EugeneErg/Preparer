<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

abstract class AbstractType implements TypeInterface
{
    private TypeCollection $results;

    public function __construct(private readonly ?AbstractFunction $functionThatReturnsThisValue = null)
    {
        $this->results = new TypeCollection([], false);
    }

    public function getFunctionThatReturnsThisValue(): ?AbstractFunction
    {
        return $this->functionThatReturnsThisValue;
    }

    protected function call(AbstractFunction $function): AbstractType
    {
        $function->context = $this;

        foreach ($this->results ?? [] as $result) {
            if ($function->equals($result->getFunctionThatReturnsThisValue())) {
                return $result;
            }
        }

        return $this->results[] = $function();
    }

    public function getResults(): TypeCollection
    {
        $result = clone $this->results;

        return $result->setImmutable(true);
    }
}
