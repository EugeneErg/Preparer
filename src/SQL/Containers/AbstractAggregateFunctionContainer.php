<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\ValueIndexService;

abstract class AbstractAggregateFunctionContainer implements AggregateFunctionContainerInterface
{
    /**
     * @var AggregateFunction[]
     */
    private array $functions = [];
    /**
     * @var self[]
     */
    private array $children = [];

    private ValueIndexService $indexService;

    public function __destructor()
    {
        $this->indexService = new ValueIndexService();
    }

    protected function createNewByFunction(string $name, array $arguments = []): self
    {
        $index = $this->indexService->getIndex($name, ...$arguments);

        if (!isset($this->children[$index])) {
            $this->children[$index] = clone $this;
            $this->children[$index]->functions[] = new AggregateFunction($name, $arguments);
        }

        return $this->children[$name][$index];
    }

    /** @inheritDoc */
    public function getFunctions(): array
    {
        return $this->functions;
    }
}