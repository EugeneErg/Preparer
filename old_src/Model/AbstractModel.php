<?php namespace EugeneErg\Preparer\Model;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Exception\CreateChildException;
use EugeneErg\Preparer\Model;
use EugeneErg\Preparer\Hasher;
use Closure;

/**
 * Class Model
 * @package EugeneErg\Preparer
 */
abstract class AbstractModel
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string|null
     */
    private $virtualType;

    /**
     * @var self[]
     */
    private static $models = [];

    /**
     * @var Container
     */
    private $container;

    private $properties = [];
    private $methods = [];
    private $offsets = [];

    /**
     * AbstractModel constructor.
     * @param mixed $value+
     */
    public function __construct($value)
    {
        $this->value = $value;


        $this->container = $this->model->getContainer();

        self::$models[$this->container->__toString()] = $this;
    }


    abstract protected function createChildByAction(AbstractAction $action, string $virtualType, ?Closure $children): ?self;

    /**
     * @param AbstractAction $action
     * @return self|null
     */
    public function createChild(AbstractAction $action): ?self
    {
        $hash = Hasher::getHash($action);

        if (isset(self::$models[$hash])) {
            return self::$models[$hash];
        }

        return self::$models[$hash] = $this->getChildWithOptions(
            function($virtualType = null, $children = null) use($action) {
                return $this->createChildByAction($action, $virtualType, $children);
            },
            [
                Property::class => $this->properties,
                Method::class => $this->methods,
                Offset::class => $this->offsets,
            ][get_class($action)],
            $action->getName(),
            $action instanceof Method ? $action->getArguments() : null
        );
    }


    /**
     * @return string|null
     */
    public function getVirtualType(): ?string
    {
        return $this->virtualType;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = (new Record())->getContainer();
            self::$models[$this->container->__toString()] = $this;
        }

        return $this->container;
    }

    /**
     * @param Record $record
     * @return Model
     */
    public static function getByRecord(Record $record)
    {
        return self::$models[$record->getContainer()->__toString()];
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param \Closure $creator
     * @param mixed $children
     * @param string|int$name
     * @param array|null $arguments
     * @return Model|null
     */
    private function getChildWithOptions(\Closure $creator, $children, $name, array $arguments = null): ?self
    {
        if ($children === false) {
            return null;
        }

        if ($children instanceof \Closure) {
            $options = function($arguments) use($children, $name) {
                return $children($name, $arguments);
            };
        }
        elseif (is_array($children)) {
            if (!array_key_exists($name, $children)) {
                return null;
            }

            $options = $children[$name];
        }
        elseif (is_string($children)) {
            if ($children !== $name) {
                return null;
            }

            $options = true;
        }
        else {
            $options = $children;
        }

        return $this->getChildForOneOptions($creator, $options, $arguments);
    }
}
