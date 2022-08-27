<?php namespace EugeneErg\Preparer\Template;

use EugeneErg\Preparer\AbstractVirtualType;
use EugeneErg\Preparer\Exception\CreateChildException;
use EugeneErg\Preparer\Model;
use EugeneErg\Preparer\Parser\AbstractTemplate;
use EugeneErg\Preparer\Record;

/**
 * Class Hash
 * @package EugeneErg\Preparer\AbstractTemplate
 */
abstract class RecordContainerHash extends AbstractTemplate
{
    const TEMPLATE = '/\$\w{32}\$/';

    /**
     * @var Record
     */
    private $record;

    /**
     * @var string[][]
     */
    private static $values = [];

    /**
     * @var AbstractVirtualType[][]|Record[][][]
     */
    private static $valueRecords = [];

    /**
     * @var Model[][]|Record[][][]
     */
    private static $modelRecords = [];

    /**
     * @var Model[]
     */
    private static $models = [];

    /**
     * RecordContainerHash constructor.
     * @param string $hash
     * @throws CreateChildException
     */
    public function __construct(string $hash)
    {
        $this->record = Record::getByHash($hash);

        parent::__construct(
            $this->recordToString($this->record)
        );
    }

    /**
     * @param Record $record
     * @param string $name
     * @return mixed|null
     */
    private function getStaticProperty(Record $record, string $name)
    {
        return self::$$name[static::class][$record->getContainer()->__toString()] ?? null;
    }

    /**
     * @param Record $record
     * @param string $name
     * @param mixed $value
     */
    private function setStaticProperty(Record $record, string $name, $value): void
    {
        self::$$name[static::class][$record->getContainer()->__toString()] = $value;//todo test it
    }

    /**
     * @param Record $record
     * @param string $name
     * @return bool
     */
    private function hasStaticProperty(Record $record, string $name): bool
    {
        return isset(self::$$name[static::class])
            && array_key_exists($record->getContainer()->__toString(), self::$$name[static::class]);
    }

    /**
     * @see RecordContainerHash::$valueRecords
     * @param Record $record
     * @return bool
     */
    private function hasValueRecords(Record $record): bool
    {
        return $this->hasStaticProperty($record, 'valueRecords');
    }

    /**
     * @see RecordContainerHash::$models
     * @param Record $record
     * @return bool
     */
    private function hasModel(Record $record): bool
    {
        return $this->hasStaticProperty($record, 'models');
    }

    /**
     * @see RecordContainerHash::$models
     * @param Record $record
     * @return Model
     */
    private function getModel(Record $record): Model
    {
        return $this->getStaticProperty($record, 'models');
    }

    /**
     * @see RecordContainerHash::$modelRecords
     * @param Record $record
     */
    private function setModelRecords(Record $record): void
    {
        $path = $record->getPath();
        $path[] = $record;

        for ($pos = count($path) - 1; $pos >= 0; $pos--) {
            if ($this->hasModel($path[$pos])) {
                $model = $this->getModel($path[$pos]);

                break;
            }
        }

        $this->setStaticProperty(
            $record,
            'modelRecords',
            isset($model)
                ? [$model, array_slice($path, $pos + 1)]
                : [Model::getByRecord($record->getRoot()), $path]
        );
    }

    /**
     * @see RecordContainerHash::$modelRecords
     * @param Record $record
     * @return bool
     */
    private function hasModelRecord(Record $record): bool
    {
        return $this->hasStaticProperty($record, 'modelRecords');
    }

    /**
     * @see RecordContainerHash::$modelRecords
     * @param Record $record
     * @return Model[]|Record[][]
     */
    private function getModelRecords(Record $record): array
    {
        return $this->getStaticProperty($record, 'modelRecords');
    }

    /**
     * @param Record $record
     * @return Model[]|Record[][]
     */
    private function getModelAndRecords(Record $record): array
    {
        if (!$this->hasModelRecord($record)) {
            $this->setModelRecords($record);
        }

        return $this->getModelRecords($record);
    }

    /**
     * @see RecordContainerHash::$modelRecords
     * @param Record $record
     * @return bool
     */
    private function hasModelRecords(Record $record): bool
    {
        return $this->hasStaticProperty($record, 'modelRecords');
    }

    /**
     * @see RecordContainerHash::$models
     * @param Record $record
     * @param Model $model
     */
    private function setModel(Record $record, Model $model): void
    {
        $this->setStaticProperty($record, 'models', $model);
    }

    /**
     * @see RecordContainerHash::$valueRecords
     * @param Record $record
     * @throws CreateChildException
     */
    private function setValueRecords(Record $record): void
    {
        list($model, $records) = $this->getModelAndRecords($record);

        foreach ($records as $pos => $childRecord) {
            $child = $model->createChildren($childRecord);

            if (is_null($child)) {
                if (!$this->hasModelRecords($childRecord)) {
                    $this->setStaticProperty($childRecord, 'modelRecords', [$model, array_slice($records, $pos)]);
                }

                $this->setStaticProperty(
                    $record,
                    'valueRecords',
                    [$model->getValue(), array_slice($records, $pos + 1)]
                );

                return;
            }

            $this->setModel($childRecord, $child);
        }

        $this->setStaticProperty($record, 'valueRecords', [$model->getValue(), []]);
    }

    /**
     * @see RecordContainerHash::$valueRecords
     * @param Record $record
     * @return AbstractVirtualType[]|Record[][]
     */
    private function getValueRecords(Record $record): array
    {
        return $this->getStaticProperty($record, 'valueRecords');
    }

    /**
     * @param Record $record
     * @return AbstractVirtualType[]|Record[][]
     * @throws CreateChildException
     */
    private function getValueAndRecords(Record $record): array
    {
        if (!$this->hasValueRecords($record)) {
            $this->setValueRecords($record);
        }

        return $this->getValueRecords($record);
    }

    /**
     * @see RecordContainerHash::$values
     * @param Record $record
     * @return string|null
     */
    private function getValue(Record $record): ?string
    {
        $this->getStaticProperty($record, 'values');
    }

    /**
     * @see RecordContainerHash::$values
     * @param Record $record
     * @return bool
     */
    private function hasValue(Record $record): bool
    {
        return $this->hasStaticProperty($record, 'values');
    }

    /**
     * @see RecordContainerHash::$values
     * @param Record $record
     * @throws CreateChildException
     */
    private function setValue(Record $record): void
    {
        list($value, $records) = $this->getValueAndRecords($record);

        $this->setStaticProperty($record, 'values', $this->valueRecordsToString($value, $records));
    }

    /**
     * @param AbstractVirtualType $value
     * @param Record[] $records
     * @return string
     */
    protected function valueRecordsToString(AbstractVirtualType $value, array $records): string
    {

    }

    /**
     * @param Record $record
     * @return string
     * @throws CreateChildException
     */
    private function recordToString(Record $record): string
    {
        if (!$this->hasValue($record)) {
            $this->setValue($record);
        }

        return $this->getValue($record);
    }
}



?>

/**
* @var string[]
*/
private $replaces = [];

/**
* @var Model[]
*/
private $models = [];

/**
* @var mixed[][]|Record[][][]
*/
private $valueRecords = [];

/**
* @var AbstractVirtualType[]
*/
private $values = [];

/**
* @var AbstractVirtualType[][]|AbstractAction[][][]
*/
private $virtualValueActions = [];

/**
* @var AbstractVirtualType[][]|Record[][][]
*/
private $virtualValueRecords= [];

/**
* @var * @return Model[][]|Record[][][]
*/
private $modelRecords = [];

/**
* @var \Closure
*/
private $callback;

/**
* @var Preparer
*/
private $preparer;

protected $objects = [

];


/**
* @inheritDoc
*/
public function translate(MainTemplate $structure): AbstractTemplate
{
foreach ($structure as $item) {
if ($item instanceof RecordContainerHash && Record::getByHash($item->__toString())) {



$this->getStringValue($item);
}
}
}

/**
* @param Record $record
* @return Model[]|Record[][]
*/
private function getModelAndRecords(Record $record): array
{
$hash = $record->getContainer()->__toString();

if (isset($this->modelRecords[$hash])) {
return $this->modelRecords[$hash];
}

$path = $record->getPath();
$path[] = $record;

for ($pos = count($path) - 1; $pos >= 0; $pos--) {
$modelHash = $path[$pos]->getContainer()->__toString();

if (isset($this->models[$modelHash])) {
$model = $this->models[$modelHash];

break;
}
}

return $this->modelRecords[$hash] = isset($model)
? [$model, array_slice($path, $pos + 1)]
: [Model::getByRecord($record->getRoot()), $path];
}

/**
* @param Record $record
* @return mixed[]|Record[][]
* @throws CreateChildException
*/

/**
* @param Record $record
* @return AbstractVirtualType[]|AbstractAction[][]
* @throws CreateChildException
*/
private function getVirtualValueAndRecords(Record $record): array
{
$hash = $record->getContainer()->__toString();

if (isset($this->virtualValueRecords[$hash])) {
return $this->virtualValueRecords[$hash];
}

$records = $record->getPath();
$records[] = $record;

for ($pos = count($records) - 1; $pos >= 0; $pos--) {
$recordHash = $records[$pos]->getContainer()->__toString();

if (isset($this->values[$recordHash])) {
$value = $this->values[$recordHash];
$records = array_slice($records, $pos + 1);

break;
}
}

if (!isset($value)) {
list($value, $records) = $this->getValueAndRecords($record);
}

foreach ($records as $pos => $record) {
//container and mixed to virtualValue
$nextValue = $record->getAction()->run($value);

if (is_null($nextValue)) {
return $this->virtualValueRecords[$hash] = [$value, array_slice($records, $pos)];
}

$recordHash = $record->getContainer()->__toString();
$this->values[$recordHash] = $value = $nextValue;
}

return $this->virtualValueRecords[$hash] = [$value, []];
}

/**
* @param Record $record
* @return VirtualVariable[]|AbstractAction[][]
* @throws CreateChildException
*/
public function getVirtualValueAndActions(Record $record): array
{
$hash = $record->getContainer()->__toString();

if (!isset($this->virtualValueActions[$hash])) {
list($virtualValue, $records) = $this->getVirtualValueAndRecords($record);
$actions = [];

foreach ($records as $record) {
$actions[] = $record->getAction();
}

$this->virtualValueActions[$hash] = [$virtualValue, $actions];
}

return $this->virtualValueActions[$hash];
}

/**
* @param RecordContainerHash $hash
* @return string
* @throws \Exception
*/
private function getStringValue(RecordContainerHash $hash): string
{
$stringHash = $hash->__toString();

if (!isset($this->replaces[$stringHash])) {
$this->replaces[$stringHash] = call_user_func_array(
$this->callback,
$this->getValueAndRecords(
$hash->getObject()
)
);
}

return $this->replaces[$stringHash];
}



/**
* @param string $query
* @return string
* @throws \ReflectionException
*/
public function prepare(string $query): string
{
return $this->translate($this->parser->parse($query))->__toString();
}

/**
* @param mixed $value
* @param Record[] $records
* @return string
*/
protected function valueRecordsToString($value, array $records): string
{


//todo actions to structure


}

/**
* @param mixed $value
* @param int|string $name
* @param virtualTypeInterface $virtualType
*/
protected function valueToVirtualType($value, $name, virtualTypeInterface $virtualType)
{

}

private function getAction(Record $record): AbstractAction
{
$hash = $record->getContainer()->__toString();

if (isset($this->actions[$hash])) {
return $this->actions[$hash];
}

$action = $record->getAction();

if (!$action instanceof Method) {
return $this->actions[$hash] = $action;
}

if ($action instanceof Method) {
$arguments = $action->getArguments();

foreach ($arguments as $argument) {

}
}

return $action;
}

private function getAction(AbstractAction $action): AbstractAction
{
$hash = Hasher::getHash($action);

if (isset($this->actions[$hash])) {
return $this->actions[$hash];
}

if (!$action instanceof Method) {

}
}

private function getProperty($value, $name, $validation)
{

}

private function getOffset($value, $name, $validation)
{

}

private function callMethod($value, $name, array $arguments, $validation)
{

}
