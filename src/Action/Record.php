<?php namespace EugeneErg\Preparer\Action;

use EugeneErg\Preparer\Record\AbstractRecord;

class Record
{
    /**
     * @var AbstractRecord
     */
    private $record;

    /**
     * Record constructor.
     * @param AbstractRecord $record
     */
    public function __construct(AbstractRecord $record)
    {
        $this->record = $record;
    }

    /**
     * @return AbstractRecord
     */
    public function getRecord(): AbstractRecord
    {
        return $this->record;
    }
}