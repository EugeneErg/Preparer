<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

use function in_array;

class ParenthesisTemplate extends AbstractTemplate
{
    public const TEMPLATE = '\\(|\\)|\\[|\\]|{|}';

    public const TYPE_CURLY = 'curly';
    public const TYPE_ROUND = 'round';
    public const TYPE_SQUARE = 'square';

    private bool $isOpen;
    private string $type;

    public function __construct(string $value)
    {
        $this->isOpen = in_array($value, ['(', '{', '['], true);

        list($this->isOpen, $this->type) = [
            '[' => [true, self::TYPE_SQUARE],
            '(' => [true, self::TYPE_ROUND],
            '{' => [true, self::TYPE_CURLY],
            ']' => [false, self::TYPE_SQUARE],
            ')' => [false, self::TYPE_ROUND],
            '}' => [false, self::TYPE_CURLY],
        ][$value];
    }

    public function isOpen(): bool
    {
        return $this->isOpen;
    }

    public function getType(): string
    {
        return $this->type;
    }
}