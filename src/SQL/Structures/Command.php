<?php namespace EugeneErg\Preparer\SQL\Structures;

use EugeneErg\Preparer\Collection;
use EugeneErg\Preparer\Parser\AbstractTemplate;

class Command
{
    private string $name;
    private Collection $includes;

    /**
     * Command constructor.
     * @param string $name
     * @param AbstractTemplate[]|Parenthesis[] $includes
     */
    public function __construct(string $name, array $includes)
    {
        $this->name = $name;
        $this->includes = new Collection($includes);
    }

    public function getIncludes(): Collection
    {
        return $this->includes;
    }

    public function getName(): string
    {
        return $this->name;
    }
}