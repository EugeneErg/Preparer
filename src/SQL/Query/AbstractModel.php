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

    /**
     * @param AbstractQuery|Union|string ...$queries
     * @return $this
     */
    public function __invoke(...$queries): self
    {
        $hash = [];

        foreach ($queries as $query) {
            $hash[] = (string) $query;
        }

        $hash = implode('.', $hash);

        if (!isset($this->contexts[$hash])) {
            $this->contexts[$hash] = clone $this;
            $this->contexts[$hash]->sources[] = $queries;
        }

        return $this->contexts[$hash];
    }
}
