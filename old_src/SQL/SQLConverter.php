<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\AbstractStructureConverter;
use EugeneErg\Preparer\Record\StructureListRecord;
use ReflectionException;

class SQLConverter extends AbstractStructureConverter
{
    /**
     * SQLConverter constructor.
     * @throws ReflectionException
     */
    public function __construct()
    {
        parent::__construct([
            //todo templates


        ]);//todo context
    }

    /**
     * @inheritDoc
     */
    public function toString(StructureListRecord $structureListRecord): string
    {
        //todo
    }

    /**
     * @inheritDoc
     */
    protected function templatesToStructure(array $templates): StructureListRecord
    {
        //todo
    }
}