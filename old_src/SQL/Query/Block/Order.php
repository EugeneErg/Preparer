<?php namespace EugeneErg\Preparer\SQL\Query\Block;

use EugeneErg\Preparer\ValueInterface;

class Order
{
    private $value;
    private bool $ascDirection;

    public function __construct(ValueInterface $value, bool $ascDirection = true)
    {
        $this->value = $value;
        $this->ascDirection = $ascDirection;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isAscDirection(): bool
    {
        return $this->ascDirection;
    }
}
