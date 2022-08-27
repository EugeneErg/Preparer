<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\Containers\QueryContainer;

use function in_array;

/**
 * @method QueryContainer getContainer()
 */
class QueryRecord extends AbstractStructureRecord
{
    protected const ACTIONS = [
        Container::ACTION_CALL => Method::class,
    ];

    private const QUERY_TYPES = ['delete', 'update', 'select'];

    public function createContainer(): QueryContainer
    {
        return new QueryContainer($this);
    }

    /** @see AbstractStructureRecord::validate */
    protected function callValidate(string $name): bool
    {
        if (!method_exists($this->getContainer(), $name)) {
            return false;
        }

        if (!in_array($name, self::QUERY_TYPES, true)) {
            return true;
        }

        foreach ($this->getActions() as $action) {
            if ($action instanceof Method
                && in_array($action->getName(), self::QUERY_TYPES, true)
            ) {
                return false;
            }
        }

        return true;
    }
}
