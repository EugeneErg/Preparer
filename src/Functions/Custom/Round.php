<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Custom;

use EugeneErg\Preparer\Enums\RoundTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\TypeInterface;

class Round extends AbstractFunction
{
    public function __construct(TypeInterface $context, public readonly RoundTypeEnum $type)
    {
        parent::__construct($context);
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->type === $function->type;
    }
}
