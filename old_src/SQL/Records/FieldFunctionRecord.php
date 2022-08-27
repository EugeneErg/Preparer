<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\Field;

class FieldFunctionRecord extends AbstractFunctionRecord
{
    protected const ACTIONS = [
        Container::ACTION_CALL => Method::class,
    ];

    private Field $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
        parent::__construct();
    }

    protected function createRecord(): FieldFunctionRecord
    {
        return new static($this->field);
    }
}