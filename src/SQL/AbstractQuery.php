<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\Containers\AggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Records\AbstractStructureRecord;
use EugeneErg\Preparer\SQL\Records\AggregateFunctionRecord;

/**
 * @mixin AggregateFunctionContainer
 */
abstract class AbstractQuery
{
    private AggregateFunctionRecord $functionRecord;
    private ?AbstractStructureRecord $structureRecord;

    public function __construct(AbstractStructureRecord $structureRecord = null)
    {
        $this->functionRecord = new AggregateFunctionRecord($this);
        $this->structureRecord = $structureRecord;
    }

    public function __call(string $name, array $arguments): Container
    {
        $functionController = $this->functionRecord->getContainer();

        return ($this->structureRecord === null || (method_exists($functionController, $name))
            ? $functionController
            : $this->structureRecord->getContainer()
        )->$name(...$arguments);
    }
}
