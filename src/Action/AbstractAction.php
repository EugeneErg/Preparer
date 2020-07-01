<?php namespace EugeneErg\Preparer\Action;

use EugeneErg\Preparer\AbstractVirtualType;

/**
 * Class Action
 * @package EugeneErg\Preparer
 */
abstract class AbstractAction
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract public function run($value);

    /**
     * @param mixed $value
     * @return bool
     */
    abstract public function has($value): bool;

    /**
     * @param AbstractVirtualType $value
     * @return string
     */
    abstract public function getResultType(AbstractVirtualType $value): ?string;

    /**
     * AbstractAction constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param AbstractVirtualType $value
     * @param string $methodName
     * @return string|null
     * @throws
     */
    protected function getMethodResultType(AbstractVirtualType $value, string $methodName): ?string
    {
        try {
            $reflectionMethod = new \ReflectionMethod(get_class($value), $methodName);
        }
        catch (\ReflectionException $exception) {
            return null;
        }

        if (!$reflectionMethod->hasReturnType()){
            return null;
        }

        return $reflectionMethod->getReturnType()->getName();
    }
}
