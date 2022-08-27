<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class CommandTemplate extends AbstractTemplate
{
    public const TEMPLATE = '\\b[a-z]+\\b';

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}