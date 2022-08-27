<?php namespace EugeneErg\Preparer\Parser;

class PatternOption
{
    private int $count;
    private string $className;

    public function __construct(int $count, string $className)
    {
        $this->count = $count;
        $this->className = $className;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}