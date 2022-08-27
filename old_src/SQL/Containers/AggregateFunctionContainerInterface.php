<?php namespace EugeneErg\Preparer\SQL\Containers;

interface AggregateFunctionContainerInterface
{
    /**
     * @return AggregateFunction[]
     */
    public function getFunctions(): array;
}