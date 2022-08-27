<?php namespace EugeneErg\Preparer;

use Closure;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Parser\MainAbstractTemplate;

/**
 * Class Preparer
 * @package EugeneErg\Preparer
 */
class Preparer extends AbstractPreparer
{
    /**
     * @var \Closure
     */
    private $callback;

    /**
     * Preparer constructor.
     * @param Closure $callback
     * @param string[]|AbstractTemplate[]|Closure $templates
     */
    public function __construct(\Closure $callback, array $templates)
    {
        $this->callback = $callback;
        $this->templates = $templates;
        parent::__construct();
    }

    /**
     * @param MainAbstractTemplate $structure
     * @return AbstractTemplate
     */
    public function translate(MainAbstractTemplate $structure): AbstractTemplate
    {
        return ($this->callback)($structure);
    }
}
