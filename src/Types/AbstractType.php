<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Collections\MixedCollection;
use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

abstract class AbstractType implements TypeInterface
{
    private readonly FunctionCollection $ancestors;
    private readonly MixedCollection $results;

    public function __construct(?FunctionCollection $ancestors = null)
    {
        $this->results = new MixedCollection([], false);
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
        $function->setContext($this);

        foreach ($this->results as [$parent, $result]) {
            if ($function->equals($parent)) {
                return $result;
            }
        }

        $result = $function();
        $this->results[] = [$function, $result];

        return $result;
    }

    public function getChildren(): FunctionCollection
    {
        return FunctionCollection::fromMap(true, fn (array $data): AbstractFunction => $data[0], $this->results);
    }

    public function __debugInfo(): array
    {
        return [
        ];
    }
}
