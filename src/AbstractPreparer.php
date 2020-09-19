<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Parser\MainAbstractTemplate;

/**
 * Class AbstractPreparer
 * @package EugeneErg\Preparer
 */
abstract class AbstractPreparer
{
    /**
     * @var string[]|AbstractTemplate[]
     */
    protected $templates = [];

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @param MainAbstractTemplate $structure
     * @return AbstractTemplate
     */
    abstract public function translate(MainAbstractTemplate $structure): AbstractTemplate;

    /**
     * AbstractPreparer constructor.
     */
    public function __construct()
    {
        $this->parser = new Parser($this->templates);
    }

    /**
     * @param string $string
     * @return string
     * @throws \ReflectionException
     */
    public function getQuery(string $string)
    {
        return $this->translate($this->parser->parse($string))->__toString();
    }
}
