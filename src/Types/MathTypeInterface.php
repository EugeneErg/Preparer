<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\RoundTypeEnum;

interface MathTypeInterface extends FieldTypeInterface
{
    public function plus(self $value): self;
    public function minus(self $value): self;
    public function times(NumericType $value): self;
    public function divided(NumericType $value, ?RoundTypeEnum $roundType = null): self;
    public function modulo(NumericType $value): self;
    public function absolute(): self;
    public function sign(): self;
}
