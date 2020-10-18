<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Functions\AllFunction;
use EugeneErg\Preparer\SQL\Functions\NotFunction;
use EugeneErg\Preparer\SQL\Functions\Traits\FunctionTrait;

abstract class AbstractModelQuery implements ModelQueryInterface
{
    use FunctionTrait {
        FunctionTrait::__construct as private functionConstructor;
        FunctionTrait::getQuery as private;
    }

    public function __construct()
    {
        $this->functionConstructor($this);
    }

    public function __get(string $name): AllFunction
    {
        /** @var AllFunction $result */
        $result = $this->getChildren(AllFunction::class, 'get', $name);

        return $result;
    }

    public function count(bool $distinct = false): NotFunction
    {
        /** @var NotFunction $result */
        $result = $this->call('count', [$distinct]);

        return $result;
    }
}
