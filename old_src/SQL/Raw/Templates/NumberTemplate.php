<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class NumberTemplate extends AbstractTemplate
{
    public const TEMPLATE = '(?:[0-9]+\\.?[0-9]*|[0-9]*\\.?[0-9]+)(?:[eE][\\-\\+]?[0-9]+)?';

    private float $value;

    public function __construct(string $value)
    {
        $this->value = (float) $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
