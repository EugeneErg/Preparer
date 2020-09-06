<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Record\AbstractTreeRecord;
use EugeneErg\Preparer\SQL\AbstractQuery;
use EugeneErg\Preparer\ToStringAsHash;

abstract class AbstractStructureRecord extends AbstractTreeRecord
{
    use ToStringAsHash {
        __toString as getStringValue;
    }

    private AbstractQuery $query;

    public function __construct(AbstractQuery $query)
    {
        $this->query = $query;
        parent::__construct();
    }

    public function getQuery(): AbstractQuery
    {
        return $this->query;
    }

    protected function createRecord(): AbstractStructureRecord
    {
        return new static($this->query);
    }
}
