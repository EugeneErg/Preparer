<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Action;
use Closure;


/**
 * Class Container
 * @package EugeneErg\Preparer
 */
class Container  implements \ArrayAccess
{
    /**
     * @var Closure
     */
    private $toString;

    /**
     * @var self[]
     */
    private $properties = [];

    /**
     * @var self[]
     */
    private $methods = [];

    /**
     * @var self[]
     */
    private $offsets = [];

    /**
     * @var Closure|null
     */
    private $callback;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Container constructor.
     * @param Closure $callback
     * @param Closure $toString
     */
    public function __construct(Closure $callback, Closure $toString)
    {
        $this->toString = $toString;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return ($this->toString)();
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

        if (!isset($this->offsets[$name])) {
            $callback = $this->callback;
            $this->offsets[$name] = $callback(new Action\Offset($name));
        }

        return $this->offsets[$name];
    }

    /**
     * @param string $name
     * @return self
     */
    public function __get(string $name): self
    {
        if (!isset($this->properties[$name])) {
            $callback = $this->callback;
            $this->properties[$name] = $callback(new Action\Property($name));
        }

        return $this->properties[$name];
    }

    /**
     * @param array $arguments
     * @return string
     */
    private function getArgumentKey(array $arguments): string
    {
        $keys = [];

        foreach ($arguments as $argument) {
            $pos = array_search($argument, $this->values, true);

            if ($pos === false) {
                $pos = count($this->values);
                $this->values[] = $argument;
            }

            $keys[] = $pos;
        }

        return implode('-', $keys);
    }

    /**
     * @param $name
     * @param array $arguments
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        $key = $this->getArgumentKey($arguments);

        if (!isset($this->methods[$name][$key])) {
            $callback = $this->callback;
            $this->methods[$name][$key] = $callback(new Action\Method($name, $arguments));
        }

        return $this->methods[$name][$key];
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
