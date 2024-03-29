<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\TypeEnum;
use EugeneErg\Preparer\Functions\Numeric\GetField;

abstract class AbstractDataType extends AbstractType implements CountableTypeInterface
{
    public function __construct(?FunctionCollection $ancestors = null)
    {
        parent::__construct($ancestors);
    }

    public function getNumeric(string $field): NumericType
    {
        /** @var NumericType $result */
        $result = $this->call(new GetField(TypeEnum::Numeric, $field));

        return $result;
    }

    public function getAngle(
        string $field,
        AngleTypeEnum $type = AngleTypeEnum::Degrees,
    ): AngleType {
        /** @var AngleType $result */
        $result = $this->call(new GetField(match ($type) {
            AngleTypeEnum::Degrees => TypeEnum::Degrees,
            AngleTypeEnum::Radians => TypeEnum::Radians,
        }, $field));

        return $result;
    }

    public function getString(string $field): StringType
    {
        /** @var StringType $result */
        $result = $this->call(new GetField(TypeEnum::String, $field));

        return $result;
    }

    public function getBoolean(string $field): BooleanType
    {
        /** @var BooleanType $result */
        $result = $this->call(new GetField(TypeEnum::Boolean, $field));

        return $result;
    }
}
