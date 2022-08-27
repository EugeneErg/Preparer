<?php namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Exception\CreateChildException;

/**
 * Class Model
 * @package EugeneErg\Preparer
 */
class Model
{
    /**
     * @var self[]
     */
    private static $models = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Closure|null
     */
    private $children;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string|null
     */
    private $virtualType;

    /**
     * @param mixed $value
     * @param string|null $virtualType
     * @param \Closure|null $children
     * @return mixed
     */
    public function __construct($value, string $virtualType = null, \Closure $children = null)
    {
        $this->value = $value;
        $this->children = $children;
        $this->virtualType = $virtualType;
    }

    /**
     * @return string|null
     */
    public function getVirtualType(): ?string
    {
        return $this->virtualType;
    }

    /**
     * @param Record $record
     * @return self|null
     * @throws CreateChildException
     */
    private static function getModel(Record $record): ?self
    {
        $hash = $record->getContainer()->__toString();

        if (!array_key_exists($hash, self::$models)) {
            $parentModel = self::getModel($record->getParent());

            if (is_null($parentModel)) {
                return null;
            }

            self::$models[$hash] = $parentModel->createChildren($record);
        }

        return self::$models[$hash];
    }

    /**
     * @param Record $record
     * @return Model|null
     * @throws CreateChildException
     */
    public function createChildren(Record $record): ?self
    {
        $hash = $record->getContainer()->__toString();

        if (isset(self::$models[$hash])) {
            return self::$models[$hash];
        }

        $creator = function($virtualType = null, $children = null) use($record) {
            return new self($record->getAction()->run($this->value), $virtualType, $children);
        };

        if (!$this->children) {
            return $creator();
        }

        $callback = $this->children;
        $result = $callback($creator, $record->getAction());

        if (!is_null($result) && !$result instanceof self) {
            throw new CreateChildException('callback method can be return Model');
        }

        return $result;
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
}
