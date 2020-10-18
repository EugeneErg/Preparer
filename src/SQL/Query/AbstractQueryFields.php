<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Containers\FunctionContainer;
use EugeneErg\Preparer\SQL\Records\AbstractStructureRecord;
use EugeneErg\Preparer\SQL\Records\ValuesRecord;

class AbstractQueryFields extends AbstractQuery
{
    private ValuesRecord $valueRecord;

    public function __construct(
        AbstractStructureRecord $structureRecord,
        bool $distinct = false,
        int $limit = null,
        int $offset = 0
    ) {
        $this->valueRecord = new ValuesRecord($this);
        parent::__construct($structureRecord, $distinct, $limit, $offset);
    }

    public function __get(string $name): FunctionContainer
    {
        return $this->valueRecord->getContainer()->$name;
    }
}
