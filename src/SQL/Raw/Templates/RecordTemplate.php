<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Hasher;
use EugeneErg\Preparer\Parser\AbstractTemplate;

class RecordTemplate extends AbstractTemplate
{
    public const TEMPLATE = ':[0-9a-z]{32}';

    /** @var object */
    private $value;
    private string $hash;

    public function __construct(string $hash)
    {
        /** @var Hasher $hasher */
        $hasher = ClassCreatorService::instance()
            ->createSingle(Hasher::class);
        $this->value = $hasher->getObject($hash);
        $this->hash = $hash;
    }

    /**
     * @return object
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
