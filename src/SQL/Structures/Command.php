<?php namespace EugeneErg\Preparer\SQL\Structures;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class Command
{
    private string $name;
    /**
     * @var AbstractTemplate[]|Parenthesis[]
     */
    private array $includes;

    /**
     * Command constructor.
     * @param string $name
     * @param AbstractTemplate[]|Parenthesis[] $includes
     */
    public function __construct(string $name, array $includes)
    {
        $this->name = $name;
        $this->includes = $includes;
    }

    /**
     * @return AbstractTemplate[]|Parenthesis[]
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