<?php namespace EugeneErg\Preparer\SQL\Containers;

class AggregateFunction
{
    private string $name;
    private array $arguments;

    public function __construct(string $name, array $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getName(): string
    {
        return $this->name;
    }
}