<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\FunctionContainer;
use EugeneErg\Preparer\SQL\Records\ValuesRecord;

class Values extends AbstractQuery
{
    private array $values;
    private ValuesRecord $valuesRecord;

    public function __construct(array ...$values)
    {
        $this->values = $values;
        $this->valuesRecord = new ValuesRecord($this);
        parent::__construct();
    }

    /**
     * @return array[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function __get(string $name): FunctionContainer
    {
        return $this->valuesRecord->getContainer()->$name;
    }
}
