<?php namespace EugeneErg\Preparer\Record;

use Closure;
use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\StringObject;

/**
 * Class RecordOld
 * @package EugeneErg\Preparer\RecordOld
 */
class RecordOld extends OldAbstractRecord
{
    /**
     * @var Closure
     */
    private $setOptions;

    /**
     * @var Closure
     */
    private $toString;

    /**
     * @var array
     */
    private $options;

    /**
     * RecordOld constructor.
     * @param Closure|null $toString
     * @param Closure|null $setOptions
     */
    public function __construct(Closure $toString = null, Closure $setOptions = null)
    {
        $this->setOptions = $setOptions;
        $this->toString = $toString;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function createByAction(AbstractAction $action): OldAbstractRecord
    {
        /** @var self $child */
        $child = parent::createByAction($action);

        if ($this->setOptions) {
            $options = ($this->setOptions)($child);

            if (is_array($options)) {
                $child->options = $options;
            }
        }

        return $child;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (!count($arguments) &&
            substr($name, 0, 3) === 'get'
        ) {
            $option = (new StringObject($name))->sub(3)->toSnakeCase()->__toString();

            return $this->options["$option"] ?? null;
        }
    }

    /**
     * @return string
     */
    protected function getStringValue(): string
    {
        return ($this->toString)($this);
    }
}
