<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class StructureFactory
 * @package EugeneErg\Preparer\Parser
 */
class StructureFactory
{
    /**
     * @var string[]
     */
    private $matches;

    /**
     * @var string|StructureInterface
     */
    private $class;

    /**
     * StructureFactory constructor.
     * @param string $class
     * @param string[] $matches
     */
    public function __construct(string $class, array $matches)
    {
        $this->matches = $matches;
        $this->class = $class;
    }

    /**
     * @param AbstractTemplate[] $children
     * @return StructureInterface
     * @throws \ReflectionException
     */
    public function createStructure(array $children): StructureInterface
    {
        $class = $this->class;
        $arguments = $this->matches;
        $arguments[$class::INCLUDE_NUMBER] = $children;

        return (new \ReflectionClass($this->class))->newInstanceArgs($arguments);
    }
}
