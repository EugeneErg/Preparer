<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class ContextAbstractTemplate
 * @package EugeneErg\Preparer\Parser
 * @property-read string $value
 */
class ContextAbstractTemplate extends AbstractTemplate
{
    /**
     * ContextAbstractTemplate constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}