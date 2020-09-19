<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class PunctuationTemplate extends AbstractTemplate
{
    public const TEMPLATE = ',|;|:|\\?';

    public const VALUE_DOT = 'dot';
    public const VALUE_SEMICOLON = 'semicolon';
    public const VALUE_QUESTION = 'question';
    public const VALUE_COLON = 'colon';

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