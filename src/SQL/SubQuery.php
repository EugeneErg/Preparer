<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\Containers\AggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Records\AggregateFunctionRecord;
use EugeneErg\Preparer\SQL\Records\SubQueryRecord;
use EugeneErg\Preparer\ToStringAsHash;

use function in_array;

/**
 * @mixin AggregateFunctionContainer
 */
class SubQuery extends AbstractQuery
{
    use ToStringAsHash {
        __toString as getStringValue;
    }

    private AggregateFunctionRecord $functionRecord;
    private SubQueryRecord $queryRecord;

    private const AGGREGATE_FUNCTION = ['count', 'exists', ''];

    public function __construct()
    {
        $this->queryRecord = new SubQueryRecord($this);
        parent::__construct();
    }

    public function __call(string $name, array $arguments): Container
    {
        return in_array($name, self::AGGREGATE_FUNCTION, true)
            ? parent::__call($name, $arguments)
            : $this->queryRecord->getContainer()->$name(...$arguments);
    }
}
