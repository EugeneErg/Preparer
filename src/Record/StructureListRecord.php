<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\Action\Record;
use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Hasher;
use ReflectionException;

class StructureListRecord extends AbstractListRecord
{
    private const ACTION_RECORD = 'record';

    protected const ACTIONS = [
        Container::ACTION_OFFSET => Offset::class,
        Container::ACTION_CALL => Method::class,
        Container::ACTION_GET => Property::class,
        self::ACTION_RECORD => Record::class
    ];

    /**
     * @var ClassCreatorService
     */
    private $classCreateService;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var string
     */
    private $hash;

    public function __construct()
    {
        $this->classCreateService = ClassCreatorService::instance();
        $this->hasher = $this->classCreateService->createSingle(Hasher::class);
        $this->hash = $this->hasher->getHash($this);
        parent::__construct();
    }

    /**
     * @param string $actionType
     * @param array $arguments
     * @return Container
     * @throws ReflectionException
     */
    public function getNext(string $actionType, array $arguments): Container
    {
        $name = reset($arguments);

        if ($actionType === Container::ACTION_GET
            && $this->hasher->hasObject($name)
        ) {
            $actionType = self::ACTION_RECORD;
            $arguments = [$this->hasher->getObject($name)];
        }
        
        return parent::getNext($actionType, $arguments);
    }

    /** @inheritDoc */
    public function getStringValue(): string
    {
        return $this->hash;
    }
}
