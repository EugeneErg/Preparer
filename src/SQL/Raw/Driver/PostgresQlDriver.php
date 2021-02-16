<?php namespace EugeneErg\Preparer\SQL\Raw\Driver;

class PostgresQlDriver extends AbstractDriverRaw
{
    protected function quoteIdentifier(string $value): string
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }

    protected function quoteInteger(int $value): string
    {
        return (string) $value;
    }

    protected function quoteFloat(float $value): string
    {
        return (string) $value;
    }

    protected function quoteString(string $value): string
    {
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
    }

    protected function quoteBoolean(bool $value): string
    {
        return ['false', 'true'][$value];
    }

    protected function quoteArray(array $value): string
    {
        return implode(',', $value);
    }

    protected function quoteObject(object $value): string
    {
        return implode(',', (array) $value);
    }

    protected function getNull(): string
    {
        return 'null';
    }
}
