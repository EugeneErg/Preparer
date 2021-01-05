<?php namespace EugeneErg\Preparer\SQL\Query;

class Table extends AbstractModel
{
    private string $name;
    private ?string $schema;
    private ?string $base;

    public function __construct(string $name, string $schema = null, string $base = null)
    {
        $this->name = $name;
        $this->schema = $schema;
        $this->base = $base;
        parent::__construct();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBase(): ?string
    {
        return $this->base;
    }

    public function getSchema(): ?string
    {
        return $this->schema;
    }
}