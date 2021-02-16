<?php namespace EugeneErg\Preparer\SQL\Raw\Driver;

class Prepare
{
    private string $query;
    private array $parameters;

    public function __construct(string $query, array $parameters)
    {
        $this->query = $query;
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}