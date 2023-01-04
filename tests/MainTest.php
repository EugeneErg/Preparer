<?php

declare(strict_types=1);

namespace Tests;

use EugeneErg\Preparer\Builder;
use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Compiler;
use EugeneErg\Preparer\Data\PreparerValue;
use EugeneErg\Preparer\Data\Table;
use EugeneErg\Preparer\Data\Value;
use EugeneErg\Preparer\Queries\InsertQuery;
use EugeneErg\Preparer\Returning;
use PHPUnit\Framework\TestCase;

final class MainTest extends TestCase
{
    public function testInsertIntoTableFromValueQuery(): void
    {
        $data = new PreparerValue();
        $query = new InsertQuery(
            new Table('table_name', 'table_schema', 'table_base'),
            new Returning(
                new TypeCollection([
                    'table_field_name' => $data->getString('value'),
                ]),
                $data,
            ),
        );
        $this->assertEquals('', (new Compiler())->toString($query, 'mysql'));
    }
}
