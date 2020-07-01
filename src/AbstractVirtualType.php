<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Action\AbstractAction;

/**
 * Class AbstractVirtualType
 * @package EugeneErg\Preparer
 */
abstract class AbstractVirtualType implements \ArrayAccess
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * AbstractVirtualType constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $name
     * @return AbstractVirtualType|null
     */
    public function __get(string $name): ?AbstractVirtualType
    {
        return call_user_func([$this, 'get' . $name . 'Attribute']);
    }

    /**
     * @param string|null $name
     * @return AbstractVirtualType|null
     */
    public function offsetGet($name = null): ?AbstractVirtualType
    {
        if (func_num_args() === 0) {
            return $this->offsetGet;
        }

        if (is_numeric($name)) {
            return $this->getOffsetNumber((float) $name);
        }

        $methodName = 'get' . $name . 'Offset';

        return method_exists($this, $methodName) ? call_user_func([$this, $methodName]) : null;
    }

    protected function getOffsetNumber(int $pos): ?AbstractVirtualType
    {
        return null;
    }

    public function offsetUnset($offset) {}

    public function offsetSet($offset, $value) {}

    public function offsetExists($offset) {}
}