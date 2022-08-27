<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Container;

class FunctionRecord extends AbstractFunctionRecord
{
    protected const ACTIONS = [
        Container::ACTION_CALL => Method::class,
    ];

    private AbstractFunctionRecord $function;

    public function __construct(AbstractFunctionRecord $function)
    {
        $this->function = $function;
        parent::__construct();
    }

    public function getFunction(): AbstractFunctionRecord
    {
        return $this->function;
    }
}