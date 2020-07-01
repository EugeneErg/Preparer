<?php namespace EugeneErg\Preparer;

/**
 * Class ObjectHelper
 * @package EugeneErg\Preparer
 */
class VariableHelper extends TypeHelper
{
    public function __construct($type)
    {
        parent::__construct(is_object($type) ? get_class($type) : gettype($type));
    }
}
