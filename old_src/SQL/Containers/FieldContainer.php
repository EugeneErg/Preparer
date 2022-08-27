<?php namespace EugeneErg\Preparer\SQL\Containers;

class FieldContainer
{
    private AddedAggregateFunctionContainer $functionContainer;

    private function __construct()
    {
        $this->functionContainer = new AddedAggregateFunctionContainer($this);
    }

    public function __get(): 
    {

    }

    public function __call(string $name, array $arguments): AddedAggregateFunctionContainer
    {
        return $this->functionContainer->$name(...$arguments);
    }
}
