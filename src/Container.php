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
     * @var Closure|null
     */
    private $getNext;

    /**
     * @var ClassCreatorService
     */
    private $classCreatorService;

    /**
     * Container constructor.
     * @param Closure $getNext
     * @param Closure $toString
     */
    public function __construct(Closure $getNext, Closure $toString)
    {
        $this->toString = $toString;
        $this->getNext = $getNext;
        $this->classCreatorService = ClassCreatorService::instance();
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

        return $this->callSingleAction(Action\Offset::class, [$name]);
    }

    /**
     * @param string $name
     * @return self
     */
    public function __get(string $name): self
    {
        return $this->callSingleAction(Action\Property::class, [$name]);
    }

    /**
     * @param $name
     * @param array $arguments
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        return $this->callSingleAction(Action\Method::class, [$name, $arguments]);
    }

    private function callSingleAction(string $class, array $arguments)
    {
        return ($this->getNext)($this->classCreatorService->createSingle($class, $arguments));
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
