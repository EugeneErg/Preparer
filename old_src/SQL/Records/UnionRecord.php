<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\SQL\Containers\FunctionContainer;

class UnionRecord extends AbstractStructureRecord
{
    public function createContainer(): FunctionContainer
    {
        return new FunctionContainer($this);
    }

    protected function getValidate(): bool
    {
        foreach ($this->getActions() as $action) {
            if ($action instanceof Method) {
                return false;
            }
        }

        return true;
    }

    protected function offsetValidate(): bool
    {
        return $this->getValidate();
    }

    public function callValidate(string $name): bool
    {
        return method_exists($this->getContainer(), $name);
    }
}