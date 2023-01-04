<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Angle\ToAngle;
use EugeneErg\Preparer\Functions\Boolean\IsNull;
use EugeneErg\Preparer\Functions\Boolean\ToBoolean;
use EugeneErg\Preparer\Functions\Numeric\ToNumeric;
use EugeneErg\Preparer\Functions\Query\GroupBy;
use EugeneErg\Preparer\Functions\Query\OrderBy;
use EugeneErg\Preparer\Functions\String\ToString;

class AggregateType extends AbstractType implements FieldTypeInterface
{
    /** @return self|NumericType|StringType|AngleType|BooleanType */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    public function groupBy(FieldTypeInterface ...$values): self
    {
        return $this->call(new GroupBy(...$values));
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        return $this->call(new OrderBy($value, $desc));
    }

    public function toNumeric(
        NumericTypeEnum $type = NumericTypeEnum::Float,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): NumericType {
        return $this->call(new ToNumeric($type, $digitsCount, $accuracy));
    }

    public function toAngle(
        AngleTypeEnum $type,
        NumericTypeEnum $numericType = NumericTypeEnum::Float,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): AngleType {
        return $this->call(new ToAngle($this, $type, $numericType, $digitsCount, $accuracy));
    }

    public function ToString(): StringType
    {
        return $this->call(new ToString());
    }

    public function toBoolean(): BooleanType
    {
        return $this->call(new ToBoolean());
    }

    public function isNull(): BooleanType
    {
        return $this->call(new IsNull());
    }
}
