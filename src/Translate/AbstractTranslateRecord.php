<?php namespace EugeneErg\Preparer\Translate;

use EugeneErg\Preparer\Hasher;
use EugeneErg\Preparer\Record\OldAbstractTypeValueRecord;

/**
 * Class HashRecordOld
 * @package EugeneErg\Preparer\Translate
 */
abstract class AbstractTranslateRecord extends OldAbstractTypeValueRecord
{
    /**
     * @var self[]
     */
    private static $records = [];

    /**
     * @var string
     */
    private $hash;

    /**
     * @return string
     */
    abstract public function valueToString(): string;

    /**
     * AbstractTranslateRecord constructor.
     * @inheritDoc
     */
    public function __construct($value)
    {
        parent::__construct($value);
        $this->hash = Hasher::getHash($this);
        self::$records[$this->hash] = $this;
    }
}
