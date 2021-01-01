<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class PunctuationTemplate extends AbstractTemplate
{
    public const TEMPLATE = ',|;|:|\\?';

    public const VALUE_DOT = ',';
    public const VALUE_SEMICOLON = ';';
    public const VALUE_QUESTION = '?';
    public const VALUE_COLON = ':';

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