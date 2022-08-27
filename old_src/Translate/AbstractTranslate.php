<?php namespace EugeneErg\Preparer\Translate;

use EugeneErg\Preparer\StringObject;
use EugeneErg\Preparer\Parser;

/**
 * Class AbstractTranslate
 * @package EugeneErg\Preparer\Translate
 */
class AbstractTranslate
{
    /**
     * @var string[]
     */
    protected $records = [];

    /**
     * @var string[]
     */
    private $recordCalls = [];

    private $parser;

    private $templates = []

    /**
     * AbstractTranslate constructor.
     */
    public function __construct()
    {
        $this->recordCalls = [];

        foreach ($this->records as $class => $alias) {
            if (is_numeric($class)) {
                $class = $alias;
                $alias = (new StringObject($alias))->rchr('\\')->__toString();
            }

            $this->recordCalls[$class] = $alias;
        }

        $this->parser = new Parser();
    }

    public function translate(string $query)
    {
        $this->
    }

}
