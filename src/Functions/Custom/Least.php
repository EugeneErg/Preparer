<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Custom;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\AbstractType;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

class Least extends AbstractFunction
{
    private readonly array $values;

    public function __construct(FieldTypeInterface ...$values)
    {
        $this->values = $values;
    }

    /** @return FieldTypeInterface[] */
    public function getValues(): array
    {
        return $this->values;
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $function->values === $this->values;
    }

    public function __invoke(): AbstractType
    {
        $class = get_class($this->context);

        foreach ($this->values as $value) {
            if (get_class($value) !== $class) {
                throw new \InvalidArgumentException('All arguments being compared must be of the same type.');
            }
        }

        return parent::__invoke();
    }
}
