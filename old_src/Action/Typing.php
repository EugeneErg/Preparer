<?php namespace EugeneErg\Preparer\Action;

use EugeneErg\Preparer\AbstractVirtualType;

/**
 * Class Typing
 * @package EugeneErg\Preparer\Action
 */
class Typing extends AbstractAction
{
    /**
     * @param AbstractVirtualType $value
     * @return string|null
     */
    public function getResultType(AbstractVirtualType $value): ?string
    {
        return $this->getName();
    }

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function run($value)
    {
        $name = $this->getName();

        if ($name === 'null') {
            return null;
        }

        if (in_array($name, ['boolean', 'array', 'object', 'integer', 'string'])) {
            return eval("return ($name) \$value");
        }

        if (class_exists($name)) {
            //todo
        }

        //todo
    }
}
