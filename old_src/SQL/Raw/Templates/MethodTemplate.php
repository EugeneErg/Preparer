<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class MethodTemplate extends AbstractTemplate
{
    public const TEMPLATE = '\\.\\s*([a-z]+)\\s*(?=\\()';

    private string $value;

    public function __construct(string $fullName, string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}