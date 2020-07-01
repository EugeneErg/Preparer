<?php namespace EugeneErg\Preparer;

use Closure;
use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;

class HashToRecordService
{
    /**
     * @var Closure
     */
    private $callback;

    /**
     * @var string[]
     */
    private $strings = [];

    /**
     * @var AbstractVirtualType[][]|Record[][][]
     */
    private $valueRecords = [];

    /**
     * @var Model[]
     */
    private $models = [];

    /**
     * @var Model[][]|Record[][][]
     */
    private $modelRecords = [];

    /**
     * @var VirtualPath[]
     */
    private $virtualPaths = [];

    /**
     * @var AbstractVirtualType[][]|Record[][][]
     */
    private $virtualRecords;

    /**
     * HashToRecordService constructor.
     * @param Closure $callback
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Record $record
     * @return Model[]|Record[][]
     *
     * get first exists model
     */
    private function getModelAndRecords(Record $record): array
    {
        return $this->getOnce($this->modelRecords, $record, static function(Record $record) {
            $path = $record->getPath();
            $path[] = $record;

            for ($pos = count($path) - 1; $pos >= 0; $pos--) {
                $model = $this->getOrNull($this->models, $path[$pos]);

                if ($model) {
                    break;
                }
            }

            if (isset($model)) {
                $path = array_slice($path, $pos + 1);
            }
            else {
                $model = Model::getByRecord($record->getRoot());
            }

            foreach ($path as $pos => $childRecord) {
                $child = $model->createChildren($childRecord);
                $childHash = $childRecord->getContainer()->__toString();

                if (is_null($child)) {
                    $this->getOnce(
                        $this->modelRecords,
                        $childRecord,
                        function() use($model, $path, $pos) {
                            return [$model, array_slice($path, $pos)];
                        }
                    );

                    return [
                        $model,
                        array_slice($path, $pos + 1)
                    ];
                }

                $this->models[$childHash] = $child;
            }
        });
    }

    /**
     * @param mixed $value
     * @param string|null|AbstractVirtualType $context
     * @return AbstractVirtualType
     */
    private function mixedToVirtualValue($value, string $context = null): AbstractVirtualType
    {

    }

    /**
     * @param Record $record
     * @return AbstractVirtualType[]|Record[][]
     */
    private function getValueAnRecords(Record $record): array
    {
        return $this->getOnce($this->valueRecords, $record, static function(Record $record) {
            list($model, $records) = $this->getModelAndRecords($record);

            $value = $model->getValue();

            for ($pos = count($records) - 1; $pos >= 0; $pos--) {
                $hash = $records[$pos]->getContainer()->__toString();

                if (isset($this->valueRecords[$hash])) {
                    $value = $this->valueRecords[$hash][0];
                    $records = array_slice($records, $pos);
                }
            }

            if (!$value instanceof AbstractVirtualType) {
                $value = $this->mixedToVirtualValue($value, $model->getVirtualType());
            }

            foreach ($records as $pos => $record) {
                $newValue = $record->getAction()->run($value);

                if ($newValue === null) {
                    return [$value, array_slice($records, $pos)];
                }

                $value = $newValue;
                $this->virtualRecords[$record->getContainer()->__toString()] = [$value, []];
            }

            if (!count($records)) {
                return [$value, []];
            }
        });
    }

    public function toString(Record $record): string
    {
        return $this->getOnce($this->strings, $record, static function(Record $record) {
            return $this->valueRecordsToString($this->getVirtualPath($record));
        });
    }

    /**
     * @param Record $record
     * @param string|null $lastVirtualType
     * @return AbstractAction
     */
    private function getAction(Record $record):AbstractAction
    {
        return $this->getOnce($this->actions, $record, static function(Record $record) {
            $this->virtualType[]

            $action = $record->getAction();

            if (!$action instanceof Method) {
                $lastVirtualType = null;

                return $action;
            }

            $arguments = [];

            foreach ($action->getArguments() as $pos => $argument) {
                if ($lastVirtualType) {
                    $argumentClass = (new TypeHelper($lastVirtualType))
                        ->getArgumentClass($action->getName(), $pos);
                }
                else {
                    $argumentClass = null;
                }

                if ($argument instanceof Container) {
                    $argument = Hasher::getObject($argument->__toString());
                }

                if (!$argument instanceof VirtualPath) {
                    if (!$argument instanceof Record) {
                        $argument = $this->mixedToVirtualValue($argument, $argumentClass);
                    }

                    $argument = $this->getVirtualPath($argument);
                }

                $arguments[] = $argument;
            }

            return new Method($action->getName(), $arguments);
        });
    }

    private function getVirtualPath(Record $record): VirtualPath
    {
        return $this->getOnce($this->virtualPaths, $record, static function(Record $record) {
            list($value, $records) = $this->getValueAnRecords($record);

            if (isset($records[0]) && $records[0]->getParent()) {
                $hash =
            }

            $lastVirtualType = get_class($value);

            foreach ($records as $record) {
                list($action, $lastVirtualType) = $this->getAction($record, $lastVirtualType);

                $actions[] = $action;
            }


        });
    }

    /**
     * @param VirtualPath $virtualPath
     * @return string
     */
    private function valueRecordsToString(VirtualPath $virtualPath): string
    {
        //
    }


    /**
     * @param array $list
     * @param Record $record
     * @param Closure $callback
     * @return mixed
     */
    private function getOnce(array &$list, Record $record, Closure $callback)
    {
        $hash = $record->getContainer()->__toString();

        if (!isset($list[$hash])) {
            $list[$hash] = $callback($record);
        }

        return $list[$hash];
    }

    /**
     * @param array $list
     * @param Record $record
     * @return mixed|null
     */
    private function getOrNull(array $list, Record $record)
    {
        return $list[$record->getContainer()->__toString()] ?? null;
    }
}
