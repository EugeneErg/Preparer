<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Angle;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\TypeInterface;

class ToAngle extends AbstractAngleFunction
{
    public function __construct(
        TypeInterface $context,
        public readonly AngleTypeEnum $angleType = AngleTypeEnum::Degrees,
        public readonly ?NumericTypeEnum $numberType = null,
        public readonly ?int $digitsCount = null,
        public readonly ?int $accuracy = null,
    ) {
        parent::__construct($context);
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->angleType === $function->angleType;
    }
}
