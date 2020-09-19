<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class StringTemplate extends AbstractTemplate
{
    public const TEMPLATE = '"(?:[^"](?:"")*)*"|\'(?:[^\'](?:\'\')*)*\'|`(?:[^`](?:``)*)*`';

    private string $value;

    public function __construct(string $value)
    {
        $this->value = str_replace($value[0] . $value[0], $value[0], substr($value, 1, -1));
    }

    public function getValue(): string
    {
        return $this->value;
    }
}