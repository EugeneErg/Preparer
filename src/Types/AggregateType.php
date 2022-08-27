<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Angle\ToAngle;
use EugeneErg\Preparer\Functions\Angle\ToNumber;
use EugeneErg\Preparer\Functions\Query\GroupBy;
use EugeneErg\Preparer\Functions\Query\OrderBy;

class AggregateType extends AbstractType
{
    /** @return self|NumericType|StringType|AngleType */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    public function groupBy(FieldTypeInterface $value): self
    {
        return $this->call(new GroupBy($value));
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        return $this->call(new OrderBy($value, $desc));
    }

    public function ToNumber(): NumericType
    {
        return $this->call(new ToNumber());
    }

    public function ToString(): StringType
    {
        return $this->call(new ToString());
    }

    public function ToAngle(): AngleType
    {
        return $this->call(new ToAngle());
    }
}
