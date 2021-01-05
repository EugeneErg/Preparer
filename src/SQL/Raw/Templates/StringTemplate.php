<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class StringTemplate extends AbstractTemplate
{
    public const TEMPLATE = '"(?:[^"](?:"")*)*"|\'(?:[^\'](?:\'\')*)*\'|`(?:[^`](?:``)*)*`';
    public const QUOTE_DOUBLE = '"';
    public const QUOTE_DEFAULT = '\'';
    public const QUOTE_SLASH = '`';

    private string $value;
    private string $quote;


    public function __construct(string $value)
    {
        $this->value = str_replace($value[0] . $value[0], $value[0], substr($value, 1, -1));
        $this->quote = $value[0];
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getQuote(): string
    {
        return $this->quote;
    }
}