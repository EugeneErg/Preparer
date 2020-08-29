<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\FunctionContainer;
use EugeneErg\Preparer\SQL\Records\FieldFunctionRecord;

/**
 * @mixin FunctionContainer
 */
class Field
{
    private string $name;
    private Table $table;
    private FieldFunctionRecord $functionRecord;

    public function __construct(Table $table, string $name)
    {
        $this->table = $table;
        $this->name = $name;
        $this->functionRecord = new FieldFunctionRecord($this);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function __call(string $name, array $arguments): FunctionContainer
    {
        return $this->functionRecord->getContainer()->$name(...$arguments);
    }
}
