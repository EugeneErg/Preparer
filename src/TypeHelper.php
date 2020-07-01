<?php namespace EugeneErg\Preparer;

class TypeHelper
{
    const TYPE_CLASS = 'class';
    const TYPE_INTERFACE = 'interface';
    const TYPE_TRAIT = 'trait';

    const TYPES = ['string', 'integer', 'boolean', 'float', 'resource'];

    /**
     * @var \ReflectionObject|string
     */
    private $type;

    /**
     * TypeHelper constructor.
     * @param string $type
     * @throws \ReflectionException
     */
    public function __construct(string $type)
    {
        $this->type = in_array($type, self::TYPES, true) ? $type : new \ReflectionClass($type);
    }

    /**
     * @return string
     */
    public function getType()
    {
        if (!$this->type instanceof \ReflectionClass) {
            return $this->type;
        }

        if ($this->type->isTrait()) {
            return self::TYPE_TRAIT;
        }

        if ($this->type->isInterface()) {
            return self::TYPE_INTERFACE;
        }

        return self::TYPE_CLASS;
    }

    /**
     * @param string $type
     * @return int
     * @throws \ReflectionException
     */
    public function getLevel($type)
    {
        if (in_array($type, self::TYPES, true)
            || !$this->type instanceof \ReflectionClass
        ) {
            return $this->getType() === $type ? 0 : null;
        }

        return $this->getTypeLevel($this->type, new \ReflectionClass($type));
    }

    /**
     * @param \ReflectionClass $current
     * @param \ReflectionClass $parent
     * @return int|null
     */
    private function getIncLevel(\ReflectionClass $current, \ReflectionClass $parent)
    {
        $getMinLevel = static function($result, $level = null) {
            return $level === null || $level > $result
                ? $result : $level;
        };

        if ($current->isTrait()) {
            return array_reduce($current->getTraits(), function($trait, $level) use($parent, $getMinLevel) {
                return $getMinLevel($this->getTypeLevel($trait, $parent), $level);
            });
        }

        $level = $getMinLevel(
            $current->getParentClass()
                ? $this->getTypeLevel($current->getParentClass(), $parent)
                : null
        );

        if ($parent->isInterface()) {
            return $getMinLevel(array_reduce($current->getInterfaces(), function($interface, $level) use($parent, $getMinLevel) {
                return $getMinLevel($this->getTypeLevel($interface, $parent), $level);
            }), $level);
        }

        if ($parent->isTrait()) {
            return $getMinLevel(array_reduce($current->getTraits(), function($trait, $level) use($parent, $getMinLevel) {
                return $getMinLevel($this->getTypeLevel($trait, $parent), $level);
            }), $level);
        }

        return $level;
    }

    /**
     * @param \ReflectionClass $parent
     * @param \ReflectionClass $current
     * @return int
     */
    private function getTypeLevel(\ReflectionClass $current, \ReflectionClass $parent)
    {
        if (!$current->isSubclassOf($parent)) {
            return null;
        }

        if ($current->getName() === $parent->getName()) {
            return 0;
        }

        $result = $this->getIncLevel($current, $parent);

        if ($result === null) {
            return $result;
        }

        return $result + 1;
    }

    public function createNew(array $arguments = [])
    {
        if ($this->type instanceof \ReflectionObject) {
            if (!count($arguments)) {
                return new $this->type->getName();
            }

            $arguments = array_values($arguments);
            $keys = array_keys($arguments);

            return eval(
                "return new {$this->type->getName()}(\$arguments[" . implode('], $arguments[', $keys) . "]);"
            );
        }
    }

    /**
     * @param string $functionName
     * @param string|int $argument
     * @return \ReflectionParameter
     * @throws \ReflectionException
     */
    public function getArgument(string $functionName, $argument): ?\ReflectionParameter
    {
        if ($this->type instanceof \ReflectionObject) {
            $parameters = $this->type->getMethod($functionName)->getParameters();
            
            if (is_numeric($argument)) {
                return $parameters[$argument];
            }
            
            foreach ($parameters as $parameter) {
                if ($parameter->getName() === $argument) {
                    return $parameter;
                }
            }
        }
    }

    /**
     * @param string $functionName
     * @param string|int $argument
     * @return string|null
     * @throws \ReflectionException
     */
    public function getArgumentClass(string $functionName, $argument): ?string
    {
        $parameter = $this->getArgument($functionName, $argument);

        if ($parameter->isArray()) {
            return 'array';
        }
        
        if ($parameter->hasType()) {
            return $parameter->getType()->getName();
        }
        
        if ($parameter->isCallable()) {
            return 'callable';//closure or other callable methods
        }
        
        if ($parameter->isOptional()) {
            return gettype($parameter->getDefaultValue());
        }

        return $parameter->getClass()->getName();//todo test it
    }
}
