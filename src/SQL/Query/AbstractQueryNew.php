<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Hasher;
use EugeneErg\Preparer\SQL\Containers\QueryContainer;
use EugeneErg\Preparer\SQL\Records\QueryRecord;
use EugeneErg\Preparer\SQL\Records\TableRecord;

abstract class AbstractQueryNew
{
    private QueryRecord $record;

    public function __construct(QueryContainer $query)
    {
        /** @var Hasher $hasher */
        $hasher = ClassCreatorService::instance()->createSingle(Hasher::class);
        /** @var TableRecord|QueryRecord $record */
        $record = $hasher->getObject($query->__toString());
        $this->record = $record;
    }

    public function getRecord(): QueryRecord
    {
        return $this->record;
    }
}
