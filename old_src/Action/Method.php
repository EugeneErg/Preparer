<?php namespace EugeneErg\Preparer\Action;

use EugeneErg\Preparer\AbstractVirtualType;

/**
 * Class Method
 * @package EugeneErg\Preparer
 */
class Method extends AbstractAction
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * Method constructor.
     * @param string $name
     * @param array $arguments
     */
    public function __construct(string $name, array $arguments)
    {
        $this->arguments = $arguments;
        parent::__construct($name);
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function run($value)
    {
        return call_user_func_array([$value, $this->getName()], $this->arguments);
    }

    /** @inheritDoc */
    public function has($value): bool
    {
        return method_exists($value, $this->getName());
    }

    /**
     * @inheritDoc
     */
    public function getResultType(AbstractVirtualType $value): ?string
    {
        return $this->getMethodResultType($value, $this->getName());
    }
}
