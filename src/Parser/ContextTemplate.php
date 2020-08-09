<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class ContextTemplate
 * @package EugeneErg\Preparer\Parser
 * @property-read string $value
 */
class ContextTemplate extends AbstractTemplate
{
    /**
     * ContextTemplate constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}