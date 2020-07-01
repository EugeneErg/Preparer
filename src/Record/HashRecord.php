<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Hasher;

/**
 * Class HashRecord
 * @package EugeneErg\Preparer\Record
 */
class HashRecord extends AbstractRecord
{
    /**
     * @var string;
     */
    private $hash;

    /**
     * @var self[]
     */
    private static $records = [];

    /** @inheritDoc */
    public function __construct()
    {
        parent::__construct();
        $this->hash = Hasher::getHash($this->getContainer());
        self::$records[$this->hash] = $this;
    }

    /**
     * @return string
     */
    protected function getStringValue(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return self
     */
    public static function getByHash(string $hash): self
    {
        return self::$records[$hash];
    }
}
