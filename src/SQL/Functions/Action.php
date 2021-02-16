<?php namespace EugeneErg\Preparer\SQL\Functions;

class Action
{
    private string $type;
    private string $name;
    private array $arguments;
    private string $method;

    public function __construct(string $method, string $name, string $type, array $arguments = [])
    {
        $this->type = $type;
        $this->method = $method;
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
