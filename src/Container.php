<?php namespace EugeneErg\Preparer;

use ArrayAccess;
use EugeneErg\Preparer\Record\AbstractRecord;
use EugeneErg\Preparer\Exception\InvalidActionException;
use ReflectionException;

class Container implements ArrayAccess
{
    public const ACTION_GET = 'get';
    public const ACTION_CALL = 'call';
    public const ACTION_OFFSET = 'offset';

    private AbstractRecord $record;

    public function __construct(AbstractRecord $record)
    {
        $this->record = $record;
    }

    public function __toString(): string
    {
        return $this->record->getStringValue();
    }

    /**
     * @param string $name
     * @return $this
     * @throws InvalidActionException
     * @throws ReflectionException
     */
    public function __get(string $name): self
    {
        return $this->record->getNext(self::ACTION_GET, [$name]);
    }

    /**
     * @param $name
     * @param array $arguments
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        return $this->record->getNext(self::ACTION_CALL, [$name, $arguments]);
    }

    /**
     * @param null $name
     * @return self
     */
    public function offsetGet($name = null): self
    {
        if (!is_string($name) || func_num_args() !== 1) {
            return $this->__call('offsetGet', func_get_args());
        }

        return $this->record->getNext(self::ACTION_OFFSET, [$name]);
    }

    /**
     * @return self
     */
    public function offsetSet($offset = null, $value = null): self
    {
        return $this->__call('offsetSet', func_get_args());
    }

    /**
     * @return self
     */
    public function offsetUnset($offset = null): self
    {
        return $this->__call('offsetUnset', func_get_args());
    }

    /**
     * @return self
     */
    public function offsetExists($offset = null): self
    {
        return $this->__call('offsetExists', func_get_args());
    }
}
