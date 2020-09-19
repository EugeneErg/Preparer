<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

class CommentTemplate extends AbstractTemplate
{
    public const TEMPLATE = '\\/\\/.*|\\/\\*.*?\\*\\/';

    private string $value;

    public function __construct(string $value)
    {
        $this->value = substr($value, 2, $value[1] === '*' ? -2 : null);
    }

    public function getValue()
    {
        return $this->value;
    }
}
