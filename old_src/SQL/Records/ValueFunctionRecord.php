<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\SQL\Value;

class ValueFunctionRecord extends AbstractSubFunctionRecord
{
    private Value $value;

    public function __construct(Value $value)
    {
        $this->value = $value;
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    protected function createRecord(): ValueFunctionRecord
    {
        return new static($this->value);
    }
}