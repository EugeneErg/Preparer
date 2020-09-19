<?php namespace EugeneErg\Preparer\SQL\Raw\Templates;

use EugeneErg\Preparer\Parser\AbstractTemplate;

use function in_array;

class OperatorTemplate extends AbstractTemplate
{
    public const TEMPLATE = '--|\\+\\+|\\*\\*|\\*|-|\\+|\\\\|%|&&|\\|\\||!!|!|<=|>=|<|>|==|===|&|\\||\\^|~|<<|>>>|>>';

    public const TYPE_MATH = 'math';
    public const TYPE_LOGIC = 'logic';
    public const TYPE_BIT = 'bit';
    public const TYPE_COMPARE = 'compare';

    public const VALUE_POWER = '**';
    public const VALUE_DECREMENT = '--';
    public const VALUE_INCREMENT = '++';
    public const VALUE_MULTIPLE = '*';
    public const VALUE_MINUS = '-';
    public const VALUE_PLUS = '+';
    public const VALUE_DELETE = '\\';
    public const VALUE_MOD = '%';

    public const VALUE_AND = '&&';
    public const VALUE_OR = '||';
    public const VALUE_TO_BOOL = '!!';
    public const VALUE_NOT = '!';

    public const VALUE_LESS_OR_EQUAL = '<=';
    public const VALUE_GREATER_OR_EQUAL = '>=';
    public const VALUE_LESS = '<';
    public const VALUE_GREATER = '>';
    public const VALUE_EQUAL = '==';
    public const VALUE_EQUAL_WITH_TYPE = '===';

    public const VALUE_BIT_AND = '&';
    public const VALUE_BIT_OR = '|';
    public const VALUE_XOR = '^';
    public const VALUE_BIT_NOT = '~';
    public const VALUE_L_SHIFT = '<<';
    public const VALUE_R_SHIFT = '>>';
    public const VALUE_R_ZERO_SHIFT = '>>>';

    private string $type;
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        $types = [
            self::TYPE_MATH => [
                self::VALUE_POWER,
                self::VALUE_DECREMENT,
                self::VALUE_INCREMENT,
                self::VALUE_MULTIPLE,
                self::VALUE_MINUS,
                self::VALUE_PLUS,
                self::VALUE_DELETE,
                self::VALUE_MOD,
            ],
            self::TYPE_LOGIC => [
                self::VALUE_AND,
                self::VALUE_OR,
                self::VALUE_TO_BOOL,
                self::VALUE_NOT,
            ],
            self::TYPE_BIT => [
                self::VALUE_BIT_AND,
                self::VALUE_BIT_OR,
                self::VALUE_XOR,
                self::VALUE_BIT_NOT,
                self::VALUE_L_SHIFT,
                self::VALUE_R_SHIFT,
                self::VALUE_R_ZERO_SHIFT,
            ],
            self::TYPE_COMPARE => [
                self::VALUE_LESS_OR_EQUAL,
                self::VALUE_GREATER_OR_EQUAL,
                self::VALUE_LESS,
                self::VALUE_GREATER,
                self::VALUE_EQUAL,
                self::VALUE_EQUAL_WITH_TYPE,
            ],
        ];

        foreach ($types as $type => $values) {
            if (in_array($value, $values, true)) {
                $this->type = $type;
                break;
            }
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }
}