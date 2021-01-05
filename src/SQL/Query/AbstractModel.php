<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Functions\AllFunction;
use EugeneErg\Preparer\SQL\Functions\NotFunction;

abstract class AbstractModel extends AbstractSource
{
    /** @var self[] */
    private array $contexts = [];
    private array $sources = [];

    public function __get(string $name): AllFunction
    {
        /** @var AllFunction $result */
        $result = $this->getChildren(AllFunction::class, 'get', $name);

        return $result;
    }

    public function count(bool $distinct = false): NotFunction
    {
        /** @var NotFunction $result */
        $result = $this->call('count', [$distinct]);

        return $result;
    }

    public function __invoke(AbstractQuery $query): self
    {
        $hash = $query->__toString();

        if (!isset($this->contexts[$hash])) {
            $this->contexts[$hash] = clone $this;
            $this->contexts[$hash]->sources[] = $query;
        }

        return $this->contexts[$hash];
    }
}
