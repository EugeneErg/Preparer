<?php namespace EugeneErg\Preparer\SQL\Structures;

use EugeneErg\Preparer\Collection;
use EugeneErg\Preparer\Parser\AbstractTemplate;

class Parenthesis
{
    private string $name;
    /**
     * @var AbstractTemplate[]|Parenthesis[]|Collection
     */
    private Collection $includes;

    /**
     * Parenthesis constructor.
     * @param string $name
     * @param AbstractTemplate[]|Parenthesis[]|Collection $includes
     */
    public function __construct(string $name, Collection $includes)
    {
        $this->name = $name;
        $this->includes = $includes;
    }

    /**
     * @return AbstractTemplate[]|Parenthesis[]|Collection
     */
    public function getIncludes(): Collection
    {
        return $this->includes;
    }

    public function getName(): string
    {
        return $this->name;
    }
}