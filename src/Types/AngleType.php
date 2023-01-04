<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Enums\RoundTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Angle\Cos;
use EugeneErg\Preparer\Functions\Angle\Cot;
use EugeneErg\Preparer\Functions\Angle\Sin;
use EugeneErg\Preparer\Functions\Angle\Tan;
use EugeneErg\Preparer\Functions\Angle\ToAngle;
use EugeneErg\Preparer\Functions\Boolean\ToBoolean;
use EugeneErg\Preparer\Functions\Custom\Absolute;
use EugeneErg\Preparer\Functions\Custom\Divided;
use EugeneErg\Preparer\Functions\Custom\Minus;
use EugeneErg\Preparer\Functions\Custom\Modulo;
use EugeneErg\Preparer\Functions\Custom\Plus;
use EugeneErg\Preparer\Functions\Custom\Sign;
use EugeneErg\Preparer\Functions\Custom\Times;
use EugeneErg\Preparer\Functions\Numeric\ToNumeric;
use EugeneErg\Preparer\Functions\String\ToString;

class AngleType extends AbstractFieldType implements MathTypeInterface
{
    /** @return NumericType|AngleType|StringType|BooleanType */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    /** @param static $value */
    public function plus(MathTypeInterface $value): self
    {
        return $this->call(new Plus($this, $value));
    }

    /** @param static $value */
    public function minus(MathTypeInterface $value): self
    {
        return $this->call(new Minus($this, $value));
    }

    public function times(NumericType $value): self
    {
        return $this->call(new Times($this, $value));
    }

    public function divided(NumericType $value, ?RoundTypeEnum $roundType = null): self
    {
        return $this->call(new Divided($this, $value, $roundType));
    }

    public function modulo(NumericType $value): self
    {
        return $this->call(new Modulo($this, $value));
    }

    public function absolute(): self
    {
        return $this->call(new Absolute());
    }

    public function sign(): self
    {
        return $this->call(new Sign());
    }

    public function cos(): NumericType
    {
        return $this->call(new Cos());
    }

    public function cot(): NumericType
    {
        return $this->call(new Cot());
    }

    public function sin(): NumericType
    {
        return $this->call(new Sin());
    }

    public function tan(): NumericType
    {
        return $this->call(new Tan());
    }

    public function toAngle(
        AngleTypeEnum $type,
        NumericTypeEnum $numericType = null,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): AngleType {
        return $this->call(new ToAngle($this, $type, $numericType, $digitsCount, $accuracy));
    }

    public function toNumeric(
        NumericTypeEnum $type = null,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): NumericType {
        return $this->call(new ToNumeric($type, $digitsCount, $accuracy));
    }

    public function toString(): StringType
    {
        return $this->call(new ToString());
    }

    public function toBoolean(): BooleanType
    {
        return $this->call(new ToBoolean());
    }
}
