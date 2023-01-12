<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

abstract class AbstractType implements TypeInterface
{
    private readonly FunctionCollection $ancestors;
    private readonly TypeCollection $results;

    public function __construct(?FunctionCollection $ancestors = null)
    {
        $this->results = new TypeCollection([], false);
        $this->ancestors = $ancestors ?? new FunctionCollection([]);
    }

    public function getAncestors(): FunctionCollection
    {
        return $this->ancestors;
    }

    public function getParent(): ?AbstractFunction
    {
        return $this->ancestors->last();
    }

    protected function call(AbstractFunction $function): AbstractType
    {
        $function->context = $this;

        foreach ($this->results as $result) {
            if ($function->equals($result->getParent())) {
                return $result;
            }
        }

        return $this->results[] = $function();
    }

    public function getChildren(): FunctionCollection
    {
        return FunctionCollection::fromMap(
            true,
            fn (AbstractType $type): AbstractFunction => $type->getAncestors()->last(),
            $this->results,
        );
    }
}
