<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Enums\RoundTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Angle\ToAngle;
use EugeneErg\Preparer\Functions\Boolean\ToBoolean;
use EugeneErg\Preparer\Functions\Custom\Absolute;
use EugeneErg\Preparer\Functions\Custom\Divided;
use EugeneErg\Preparer\Functions\Custom\Minus;
use EugeneErg\Preparer\Functions\Custom\Modulo;
use EugeneErg\Preparer\Functions\Custom\Plus;
use EugeneErg\Preparer\Functions\Custom\Round;
use EugeneErg\Preparer\Functions\Custom\Sign;
use EugeneErg\Preparer\Functions\Custom\Times;
use EugeneErg\Preparer\Functions\Numeric\Acos;
use EugeneErg\Preparer\Functions\Numeric\Asin;
use EugeneErg\Preparer\Functions\Numeric\Atan;
use EugeneErg\Preparer\Functions\Numeric\BitAnd;
use EugeneErg\Preparer\Functions\Numeric\BitNot;
use EugeneErg\Preparer\Functions\Numeric\BitOr;
use EugeneErg\Preparer\Functions\Numeric\BitXor;
use EugeneErg\Preparer\Functions\Numeric\Exp;
use EugeneErg\Preparer\Functions\Numeric\Factorial;
use EugeneErg\Preparer\Functions\Numeric\LeftShift;
use EugeneErg\Preparer\Functions\Numeric\Ln;
use EugeneErg\Preparer\Functions\Numeric\Log;
use EugeneErg\Preparer\Functions\Numeric\Power;
use EugeneErg\Preparer\Functions\Numeric\RightShift;
use EugeneErg\Preparer\Functions\Numeric\Root;
use EugeneErg\Preparer\Functions\Numeric\Scale;
use EugeneErg\Preparer\Functions\Numeric\ToNumeric;
use EugeneErg\Preparer\Functions\String\ToString;

class NumericType extends AbstractFieldType implements MathTypeInterface
{
    /** @return NumericType|AngleType|StringType|BooleanType */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    /** @param NumericType $value */
    public function plus(MathTypeInterface $value): self
    {
        return $this->call(new Plus($this, $value));
    }

    /** @param NumericType $value */
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

    public function power(self $value): self
    {
        return $this->call(new Power($this, $value));
    }

    public function root(self $value): self
    {
        return $this->call(new Root($this, $value));
    }

    public function bitOr(self $value): self
    {
        return $this->call(new BitOr($this, $value));
    }

    public function bitXor(self $value): self
    {
        return $this->call(new BitXor($this, $value));
    }

    public function bitAnd(self $value): self
    {
        return $this->call(new BitAnd($this, $value));
    }

    public function leftShift(self $value): self
    {
        return $this->call(new LeftShift($this, $value));
    }

    public function rightShift(self $value): self
    {
        return $this->call(new RightShift($this, $value));
    }

    public function log(self $base): self
    {
        return $this->call(new Log($base));
    }

    public function round(RoundTypeEnum $type = RoundTypeEnum::Nearest): self
    {
        return $this->call(new Round($this, $type));
    }

    public function factorial(): self
    {
        return $this->call(new Factorial($this));
    }

    public function absolute(): self
    {
        return $this->call(new Absolute($this));
    }

    public function bitNot(): self
    {
        return $this->call(new BitNot($this));
    }

    public function ln(): self
    {
        return $this->call(new Ln($this));
    }

    public function exp(): self
    {
        return $this->call(new Exp($this));
    }

    public function scale(): self
    {
        return $this->call(new Scale($this));
    }

    public function sign(): self
    {
        return $this->call(new Sign($this));
    }

    public function acos(AngleTypeEnum $angleType = AngleTypeEnum::Degrees): AngleType
    {
        return $this->call(new Acos($this, $angleType));
    }

    public function asin(AngleTypeEnum $angleType = AngleTypeEnum::Degrees): AngleType
    {
        return $this->call(new Asin($this, $angleType));
    }

    public function atan(AngleTypeEnum $angleType = AngleTypeEnum::Degrees): AngleType
    {
        return $this->call(new Atan($this, $angleType));
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
        NumericTypeEnum $type,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): NumericType {
        return $this->call(new ToNumeric($type, $digitsCount, $accuracy));
    }

    public function toString(): StringType
    {
        return $this->call(new ToString($this));
    }

    public function toBoolean(): BooleanType
    {
        return $this->call(new ToBoolean($this));
    }
}
