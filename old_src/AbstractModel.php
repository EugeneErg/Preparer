<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;

/**
 * Class AbstractModel
 * @package EugeneErg\Preparer
 */
abstract class AbstractModel
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var string|null
     */
    protected $virtualType;

    /**
     * @var mixed
     */
    protected $properties = false;

    /**
     * @var mixed
     */
    protected $methods = false;

    /**
     * @var mixed
     */
    protected $offsets = false;

    /**
     * @var self[]
     */
    private static $models = [];

    /**
     * AbstractModel constructor.
     * @param mixed $value
     * @throws \Exception
     */
    public function __construct($value)
    {
        $this->model = new Model(
            $value,
            $this->virtualType,
            function(\Closure $creator, AbstractAction $action) {
                return $this->getChildWithOptions(
                    $creator,
                    [
                        Property::class => $this->properties,
                        Method::class => $this->methods,
                        Offset::class => $this->offsets,
                    ][get_class($action)],
                    $action->getName(),
                    $action instanceof Method ? $action->getArguments() : null
                );
            }
        );
        $this->container = $this->model->getContainer();

        self::$models[$this->container->__toString()] = $this;
    }

    /**
     * @param \Closure $creator
     * @param mixed $options
     * @param array|null $arguments
     * @return mixed|null
     */
    private function getChildForOneOptions(\Closure $creator, $options, array $arguments = null)
    {
        if ($options === true) {
            return $creator(null, function() {
                return null;
            });
        }

        if ($options === false) {
            return null;
        }

        if ($options === null) {
            return $creator();
        }

        if (is_subclass_of($options, self::class)) {
            return (new $options($creator()->getValue()))->model;
        }

        if (is_subclass_of($options, AbstractVirtualType::class)) {
            return $creator($options);
        }

        if ($options instanceof \Closure) {
            return $options($creator, $arguments);
        }

        if (is_array($options)) {
            return $creator(
                isset($options['role']) ? $options['role'] : null,
                function(\Closure $creator, AbstractAction $action) use($options) {
                    return $this->getChildWithOptions(
                        $creator,
                        [
                            Property::class => isset($options['properties']) ? $options['properties'] : false,
                            Method::class => isset($options['methods']) ? $options['methods'] : false,
                            Offset::class => isset($options['offsets']) ? $options['offsets'] : false,
                        ][get_class($action)],
                        $action->getName(),
                        $action instanceof Method ? $action->getArguments() : null
                    );
                }
            );
        }

        if ($options) {
            return $creator(null, function() {
                return null;
            });
        }

        return null;
    }
}
