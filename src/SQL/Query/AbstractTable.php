<?php namespace EugeneErg\Preparer\SQL\Query;

abstract class AbstractTable extends Table
{
    public const NAME = null;
    public const SCHEMA = null;
    public const BASE = null;

    public function __construct()
    {
        parent::__construct(static::NAME, static::SCHEMA, static::BASE);
    }
}
