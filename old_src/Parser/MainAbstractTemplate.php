<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class MainAbstractTemplate
 * @package EugeneErg\Preparer\Parser
 */
class MainAbstractTemplate extends AbstractTemplate implements StructureInterface
{
    /**
     * @var AbstractTemplate[]
     */
    private $structure;

    /**
     * @var string
     */
    private $value;

    /**
     * MainAbstractTemplate constructor.
     * @param AbstractTemplate[] $structure
     */
    public function __construct(array $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return AbstractTemplate[]
     */
    public function getChildren(): array
    {
        return $this->structure;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (isset($this->value)) {
            return $this->value;
        }

        $value = [];

        foreach ($this->structure as $item) {
            $value[] = $item->__toString();
        }

        return $this->value =implode('', $value);
    }
}
