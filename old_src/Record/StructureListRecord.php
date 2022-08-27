<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\Action\Record;
use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Hasher;
use EugeneErg\Preparer\ToStringAsHash;
use ReflectionException;

class StructureListRecord extends AbstractListRecord
{
    use ToStringAsHash {
        __toString as getStringValue;
    }

    private const ACTION_RECORD = 'record';

    protected const ACTIONS = [
        Container::ACTION_OFFSET => Offset::class,
        Container::ACTION_CALL => Method::class,
        Container::ACTION_GET => Property::class,
        self::ACTION_RECORD => Record::class
    ];

    private ClassCreatorService $classCreateService;
    private Hasher $hasher;

    public function __construct()
    {
        $this->classCreateService = ClassCreatorService::instance();
        /** @var Hasher $hasher */
        $hasher = $this->classCreateService->createSingle(Hasher::class);
        $this->hasher = $hasher;
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
            $subRecord = $this->hasher->getObject($name);

            if ($subRecord instanceof StructureListRecord) {
                $actionType = self::ACTION_RECORD;
                $arguments = [$subRecord];
            }
        }
        
        return parent::getNext($actionType, $arguments);
    }
}
