<?php namespace EugeneErg\Preparer\SQL\Functions;

class Action
{
    private string $type;
    private string $name;
    private array $arguments;

    public function __construct(string $type, string $name, array $arguments = [])
    {
        $this->type = $type;
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
}
