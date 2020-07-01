<?php namespace EugeneErg\Preparer\Parser;

/**
 * Class AbstractTemplate
 * @package EugeneErg\Preparer\Parser
 */
abstract class AbstractTemplate
{
    const TEMPLATE = '.*';

    /**
     * @var string
     */
    private $value;

    /**
     * AbstractTemplate constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
