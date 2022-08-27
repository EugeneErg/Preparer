<?php namespace EugeneErg\Preparer\SQL\Raw\Driver;

use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Parser\Parser;
use EugeneErg\Preparer\SQL\Functions\AbstractFunction;
use EugeneErg\Preparer\SQL\Functions\Action;
use EugeneErg\Preparer\SQL\Query\AbstractTable;
use EugeneErg\Preparer\SQL\Raw\RawException;
use EugeneErg\Preparer\SQL\Raw\Templates\AllTemplate;
use EugeneErg\Preparer\SQL\Raw\Templates\RecordTemplate;
use EugeneErg\Preparer\SQL\Value;

abstract class AbstractDriverRaw
{
    private Parser $parser;
    private ClassCreatorService $classCreatorService;

    public function __construct()
    {
        $this->parser = new Parser([RecordTemplate::class], AllTemplate::class);
        $this->classCreatorService = ClassCreatorService::instance();
    }

    abstract protected function quoteIdentifier(string $value): string;
    abstract protected function quoteInteger(int $value): string;
    abstract protected function quoteFloat(float $value): string;
    abstract protected function quoteString(string $value): string;
    abstract protected function quoteBoolean(bool $value): string;
    abstract protected function quoteArray(array $value): string;
    abstract protected function quoteObject(object $value): string;
    abstract protected function getNull(): string;

    /**
     * @param string $query
     * @return string
     * @throws RawException
     */
    public function toString(string $query): string
    {
        $results = [];

        return $this->handlePartials($query, [
            function(AllTemplate $partial): string {
                return $partial->getValue();
            },
            function(RecordTemplate $partial) use(&$results): string {
                if (!isset($results[$partial->getHash()])) {
                    $results[$partial->getHash()] = $this->quoteValue($partial->getValue());
                }

                return $results[$partial->getHash()];
            },
        ]);
    }

    /**
     * @param string $query
     * @return Prepare
     * @throws RawException
     */
    public function toPrepare(string $query): Prepare
    {
        $results = [];
        $parameters = [];
        $query = $this->handlePartials($query, [
            function(AllTemplate $partial): string {
                return $partial->getValue();
            },
            function(RecordTemplate $partial) use(&$parameters, &$results): string {
                if (!isset($results[$partial->getHash()])) {
                    $results[$partial->getHash()] = $this->prepareValue($partial->getValue(), $parameters);
                }

                return $results[$partial->getHash()];
            },
        ]);

        return new Prepare($query, $parameters);
    }

    private function getAlias(AbstractTable $record): string
    {
        return $this->quoteIdentifier($record->__toString());
    }

    /**
     * @param AbstractTable $source
     * @param Action[] $actions
     * @return string
     * @throws RawException
     */
    private function quoteField(AbstractTable $source, array $actions): string
    {
        if (count($actions) > 1
            || $actions[0]->getMethod() !== 'get'
        ) {
            throw new RawException();
        }

        return $this->getAlias($source) . '.' . $this->quoteIdentifier($actions[0]->getName());
    }

    /**
     * @param mixed $value
     * @param Action[] $actions
     * @return mixed
     * @throws RawException
     */
    private function applyActions($value, array $actions): string
    {
        foreach ($actions as $action) {
            switch ($action->getMethod()) {
                case 'get':
                case 'offset':
                    $value = is_object($value)
                        ? $value->{$action->getName()} ?? null
                        : $value[$action->getName()] ?? null;
                    break;
                default:
                    throw new RawException();
            }
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws RawException
     */
    private function quoteValue($value): string
    {
        if ($value instanceof AbstractTable) {
            return $this->quoteTable($value);
        }

        if (!$value instanceof AbstractFunction) {
            $source = $value->getSource();

            if ($source instanceof AbstractTable) {
                return $this->quoteField($source, $value->getActions());
            }

            if ($source instanceof Value) {
                return $this->quoteValue($this->applyActions($source, $value->getActions()));
            }
        }

        switch (gettype($value)) {
            case 'integer':
                return $this->quoteInteger($value);
            case 'float':
                return $this->quoteFloat($value);
            case 'boolean':
                return $this->quoteBoolean($value);
            case 'string':
                return $this->quoteString($value);
            case 'NULL':
                return $this->getNull();
            case 'array':
                $result = [];

                foreach ($value as $key => $val) {
                    $result[$key] = is_array($val) || is_object($val)
                        ? '(' . $this->quoteValue($val) . ')'
                        : $this->quoteValue($val);
                }

                return $this->quoteArray($result);
            case 'object':
                $result = new \stdClass();

                foreach ($value as $key => $val) {
                    $result->$key = is_array($val) || is_object($val)
                        ? '(' . $this->quoteValue($val) . ')'
                        : $this->quoteValue($val);
                }

                return $this->quoteObject($result);
            default:
                throw new RawException();
        }
    }

    /**
     * @param string $query
     * @param \Closure ...$handles
     * @return string
     * @throws \ReflectionException
     */
    private function handlePartials(string $query, \Closure ...$handles): string
    {
        $templatesHandles = [];

        foreach ($handles as $handle) {
            $reflection = new \ReflectionFunction($handle);
            $templatesHandles[$reflection->getParameters()[0]->getClass()->getName()] = $handles;
        }

        $templates = $this->parser->parse($query);
        $result = [];

        foreach ($templates as $template) {
            $handler = $templatesHandles[get_class($template)];
            $result[] = $handler($template);
        }

        return implode('', $result);
    }

    private function quoteTable(AbstractTable $record): string
    {
        return ($record->getSchema() === null ? '' : $this->quoteIdentifier($record->getSchema()) . '.')
            . ($record->getBase() === null ? '' : $this->quoteIdentifier($record->getBase()) . '.')
            . $this->quoteIdentifier($record->getName()) . ' as ' . $this->getAlias($record);
    }

    private function prepareObject(object $value, array &$parameters): string
    {
        $result = new \stdClass();

        foreach ($value as $key => $val) {
            $result->$key = is_array($val) || is_object($val)
                ? '(' . $this->prepareValue($val, $parameters) . ')'
                : $this->prepareValue($val, $parameters);
        }

        return $this->quoteObject($result);
    }

    private function prepareArray(array $value, array &$parameters): string
    {
        $result = [];

        foreach ($value as $key => $val) {
            $result[$key] = is_array($val) || is_object($val)
                ? '(' . $this->prepareValue($val, $parameters) . ')'
                : $this->prepareValue($val, $parameters);
        }

        return $this->quoteArray($result);
    }

    private function prepareValue($value, &$parameters): string
    {
        if ($value instanceof AbstractTable) {
            return $this->quoteTable($value);
        }

        if ($value instanceof AbstractFunction) {
            $source = $value->getSource();

            if ($source instanceof AbstractTable) {
                return $this->quoteField($source, $value->getActions());
            }

            if ($source instanceof Value) {
                $value = $this->applyActions($source, $value->getActions());

                if (is_object($value)) {
                    return $this->prepareObject($value, $parameters);
                }

                if (is_array($value)) {
                    return $this->prepareArray($value, $parameters);
                }

                $hash = $value->__toString();
                $parameters[substr($hash, 1)] = $value;

                return $hash;
            }
        }

        if (is_object($value)) {
            return $this->prepareObject($value, $parameters);
        }

        if (is_array($value)) {
            return $this->prepareArray($value, $parameters);
        }

        $result = md5(gettype($value) . (string) $value);
        $parameters[':' . $result] = $value;

        return $result;
    }
}
