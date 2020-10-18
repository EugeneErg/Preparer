<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\FunctionContainer;
use EugeneErg\Preparer\SQL\Query\SelectQueryInterface;
use EugeneErg\Preparer\SQL\Records\UnionRecord;
use EugeneErg\Preparer\ToStringAsHash;

class Union extends AbstractQuery implements SelectQueryInterface
{
    use ToStringAsHash {
        __toString as getStringValue;
    }

    private bool $all;
    /**
     * @var SelectQueryInterface[]
     */
    private array $queries;
    private UnionRecord $unionRecord;

    public function __construct(bool $all, SelectQueryInterface ...$queries)
    {
        $this->all = $all;
        $this->queries = $queries;
        $this->unionRecord = new UnionRecord($this);
        parent::__construct();
    }

    public function isAll(): bool
    {
        return $this->all;
    }

    /**
     * @return SelectQueryInterface[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    public function __get(string $name): FunctionContainer
    {
        return $this->unionRecord->getContainer()->$name;
    }
}
