<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Hasher;
use EugeneErg\Preparer\SQL\Containers\QueryContainer;
use EugeneErg\Preparer\SQL\Containers\TableContainer;
use EugeneErg\Preparer\SQL\Records\AbstractStructureRecord;
use EugeneErg\Preparer\SQL\Records\QueryRecord;
use EugeneErg\Preparer\SQL\Records\TableRecord;

class AbstractSqlConverter
{
    /**
     * @param TableContainer|QueryContainer $query
     * @return string
     */
    public function toString(Container $query): string
    {
        /** @var Hasher $hasher */
        $hasher = ClassCreatorService::instance()->createSingle(Hasher::class);
        /** @var TableRecord|QueryRecord $record */
        $record = $hasher->getObject($query->__toString());
        $this->getStructure($record);




    }

    private function getStructure(AbstractStructureRecord $record)
    {
        $query = $record->getQuery();
        $actions = $record->getActions();


    }


}