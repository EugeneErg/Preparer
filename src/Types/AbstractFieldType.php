<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Boolean\IsNull;

class AbstractFieldType extends AbstractType implements FieldTypeInterface
{
    /** @return BooleanType */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    public function isNull(): BooleanType
    {
        return $this->call(new IsNull());
    }
}
