<?php namespace EugeneErg\Preparer\SQL\Structures;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class Parenthesis
{
    private string $name;
    /**
     * @var AbstractTemplate[]
     */
    private array $includes;

    /**
     * Parenthesis constructor.
     * @param string $name
     * @param AbstractTemplate[] $includes
     */
    public function __construct(string $name, array $includes)
    {
        $this->name = $name;
        $this->includes = $includes;
    }

    /**
     * @return AbstractTemplate[]
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }

    public function getName(): string
    {
        return $this->name;
    }
}