<?php namespace EugeneErg\Preparer\SQL\Raw;

final class Structure
{
    /**
     * @var Structure[]
     */
    private array $children = [];

    public function addChild(string $name, Structure $child = null)
    {
        $this->children[$name] = $child;
    }

    /**
     * @param Structure[]|string[] $children
     */
    public function addChildren(array $children)
    {
        foreach ($children as $name => $child){
            if (is_numeric($name)) {
                $this->addChild($child);
            } else {
                $this->addChild($name, $child);
            }
        }
    }

    public function getChild(string $name)
    {
        return $this->children[$name];
    }

    /**
     * @return string[]
     */
    public function getChildNames(): array
    {
        return array_keys($this->children);
    }
}