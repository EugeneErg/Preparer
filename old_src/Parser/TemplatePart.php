<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class TemplatePart
 * @package EugeneErg\Preparer
 */
class TemplatePart
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var self[]
     */
    private $children = [];

    /**
     * @var self
     */
    private $parent;

    /**
     * @var bool[]
     */
    private $map;

    /**
     * TemplatePart constructor.
     * @param string $class
     * @param int $from
     * @param int $to
     * @param string[] $arguments
     * @param bool[] $map
     */
    public function __construct(string $class, int $from, int $to, array $arguments, array $map)
    {
        $this->class = $class;
        $this->from = $from;
        $this->to = $to;
        $this->arguments = $arguments;
        $this->map = $map;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param self $child
     */
    public function addChild(self $child): void
    {
        $child->parent = $this;
        $this->children[] = $child;
    }

    /**
     * @return TemplatePart[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return TemplatePart|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @return bool[]
     */
    public function getMap(): array
    {
        return $this->map;
    }
}
