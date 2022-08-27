<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\SQL\Containers\TableContainer;
use EugeneErg\Preparer\SQL\Records\TableRecord;

/**
 * @mixin TableContainer
 */
class Table extends AbstractQuery
{
    private string $name;
    private ?string $schema;
    private ?string $base;

    public function __construct(string $name, string $schema = null, $base = null)
    {
        $this->name = $name;
        $this->schema = $schema;
        $this->base = $base;
        parent::__construct(new TableRecord($this));
    }

    public function __get(string $name): Field
    {
        /** @var Field $result */
        $result = ClassCreatorService::instance()
            ->createSingle(Field::class, [$this, $name]);

        return $result;
    }
}
